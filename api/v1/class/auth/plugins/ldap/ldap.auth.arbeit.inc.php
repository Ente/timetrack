<?php
namespace Arbeitszeit {
    use LdapTools\Configuration;
    use LdapTools\LdapManager;
    use LdapTools\DomainConfiguration;
    use LdapTools\Operation\AuthenticationOperation;
    use Arbeitszeit\Events\LDAPAuthenticationEvent;
    use Arbeitszeit\Events\EventDispatcherService;
    class LDAP extends Auth {

        public static function get_bind(){
            Exceptions::error_rep("Getting LDAP bind...");
            $settings = Arbeitszeit::get_app_ini()["ldap"];
            $configuration = new Configuration();
            $domain = (new DomainConfiguration($settings["ldap_domain"]))
                ->setBaseDn($settings["ldap_basedn"])
                ->setServers(["{$settings["ldap_ip"]}"])
                ->setUsername($settings["ldap_user"])
                ->setUsePaging(true)
                ->setPassword(base64_decode($settings["ldap_password"]));
            if($settings["saf"] != "true" || $settings["saf"] == false){
                $altdomain = (new DomainConfiguration($settings["saf_domain"]))
                ->setBaseDn($settings["saf_basedn"])
                ->setServers(["{$settings["saf_ip"]}"])
                ->setUsername($settings["saf_user"])
                ->setPassword(base64_decode($settings["saf_password"]))
                ->setLazyBind(true)
                ->setUsePaging(true)
                ->setLdapType("AD");
            } else {
                $altdomain = (new DomainConfiguration($settings["ldap_domain"]))
                ->setBaseDn($settings["ldap_basedn"])
                ->setServers(["{$settings["ldap_ip"]}"])
                ->setUsername($settings["ldap_user"])
                ->setPassword(base64_decode($settings["ldap_password"]))
                ->setLazyBind(true)
                ->setUsePaging(true)
                ->setLdapType("AD");
            }
            $configuration->addDomain($domain, $altdomain);
            $configuration->setDefaultDomain($settings["ldap_domain"]);
            $ldap = new LdapManager($configuration);
            return $ldap;
        }

        public static function authenticate($username, $password){
            Exceptions::error_rep("Authenticating user '{$username}' through LDAP...");
            $operation = (new AuthenticationOperation())->setUsername($username)->setPassword($password);
            $response = self::get_bind()->getConnection()->execute($operation);
            $code1 = null;
            if(!$response->isAuthenticated()){
                $code = $code1;
                $code = $response->getErrorCode();
                switch($code){
                    case "1317":
                        Exceptions::error_rep("Could not authenticate user '{$username}': Account does not exist.");
                        break;
                    case "1326":
                        Exceptions::error_rep("Could not authenticate user '{$username}': Account password invalid.");
                        break;
                    case "1327":
                        Exceptions::error_rep("Could not authenticate user '{$username}': Account restrictions are set.");
                        break;
                    case "1328":
                        Exceptions::error_rep("Could not authenticate user '{$username}': The account cannot login at this time due to time restrictions.");
                        break;
                    case "1329":
                        Exceptions::error_rep("Could not authenticate user '{$username}': The account is not allowed to log on to this device.");
                        break;
                    case "1330":
                        Exceptions::error_rep("Could not authenticate user '{$username}': Account password has expired.");
                        break;
                    case "1331":
                        Exceptions::error_rep("Could not authenticate user '{$username}': Account disabled.");
                        break;
                    case "1384":
                        Exceptions::error_rep("Could not authenticate user '{$username}': Account has too many memberships in groups.");
                        break;
                    case "1793":
                        Exceptions::error_rep("Could not authenticate user '{$username}': Account expired.");
                        break;
                    case "1907":
                        Exceptions::error_rep("Could not authenticate user '{$username}': The accounts password has to be changed.");
                        break;
                    case "1909":
                        Exceptions::error_rep("Could not authenticate user '{$username}': Account locked.");
                        break;
                }
                Exceptions::error_rep("Could not authenticate user '{$username}': LDAP error code: {$code}");
                die(Auth::login($username, $password, ["LOCAL" => true]));
            } else {
                try {
                    Exceptions::error_rep("Building LDAP query...");
                    $query = self::get_bind()->buildLdapQuery();
                    $user = $query->select()
                        ->select(["sid", "mail", "upn", "firstName", "lastName"])
                        ->fromUsers()
                        ->where(["username" => $username])
                        ->andWhere($query->filter()->isRecursivelyMemberOf(Arbeitszeit::get_app_ini()["ldap"]["ldap_group"]))
                        ->getLdapQuery()
                        ->getSingleResult();

                    if(isset($user)){
                        if(Benutzer::get_user($username) == false){
                        
                            Exceptions::error_rep("Could not authenticate user '{$username}': User not found in DB.");
                            if(Arbeitszeit::get_app_ini()["ldap"]["create_user"] == "true"){
                                $benutzer = new Benutzer;
                                Exceptions::error_rep("User authenticated but not existent locally '{$username}': Trying to create user instead...");
                                if($user->get("mail") == ""){
                                    Exceptions::error_rep("Could not authenticate user '{$username}': User email in LDAP not set!");
                                    return false;
                                }
                                if(!$benutzer->create_user($username, "" . $user->get("firstName") . " " . $user->get("lastName"), $user->get("mail"), hash('sha256', $user->get("sid"). $user->get("upn")), 0)){
                                    Exceptions::error_rep("Could not authenticate user '{$username}': Could not create user in DB.");
                                    return false;  
                                } else {
                                    $statusMessages = new StatusMessages;
                                    $uri = $statusMessages->URIBuilder("ldapcreated");
                                    header("Location: http://". Arbeitszeit::get_app_ini()["general"]["base_url"]."/suite/" . $uri);
                                }
                            }
                            return false;
                        }
                    }
                
                } catch (\LdapTools\Exception\EmptyResultException $e){
                    Exceptions::error_rep("Exception thrown, probably no LDAP results: " . $e->getMessage());
                    return false;
                }
                $_SESSION["provider"] = "LDAP";
                $_SESSION["provider"] .= "." . $user->get("upn") . "+" . $user->get("sid");
                Exceptions::error_rep("Successfully authenticated user '{$username}' through LDAP.");
                EventDispatcherService::get()->dispatch(new LDAPAuthenticationEvent($username, $user->get("mail"), "LDAP" . $code1), LDAPAuthenticationEvent::NAME);
                return true;
            }
        }
    }
}

?>