<?php
namespace Arbeitszeit{
    use Arbeitszeit\Mails\Provider\PHPMailerMailsProvider;
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;
    use Arbeitszeit\Events\EventDispatcherService;
    use Symfony\Component\EventDispatcher\EventDispatcher;
    use Arbeitszeit\Events\LoggedInUserEvent;
    use Arbeitszeit\Events\LoggedOutUserEvent;
    use Arbeitszeit\Events\ValidatedLoginEvent;
    use LdapTools\Event\Event;

    class Auth extends Arbeitszeit{

        public $db;

        public function __construct(){
            $this->db = new DB;
        }
        
        public static function login($username, $password, $option){ # "option"-> array [ "remember" => true/false, ... ]

            Exceptions::error_rep("Logging in user '$username'...");
            $db = new DB;
            session_start();
            $ini = Arbeitszeit::get_app_ini();
            $base_url = $ini["general"]["base_url"];
            $username = preg_replace("/\s+/", "", $username);

            if($ini["ldap"]["ldap"] == "true" && !isset($option["LOCAL"]) && !isset($option["nfclogin"])){
                if(LDAP::authenticate($username, $password) != true){
                    EventDispatcherService::get()->dispatch(new LoggedInUserEvent($username, "failed"));
                    Exceptions::error_rep("Login failed for username '$username' - Could not authenticate user via LDAP. See errors from before to find issue.");
                    die(header("Location: http://{$ini["general"]["base_url"]}/suite/login.php?error=ldapauth"));
                } else {
                    Exceptions::error_rep("Successfully authenticated user '" . $username . "' - LDAP Auth");
                    $ldap = true;
                }
            }
            if(!isset($username, $password)){
                EventDispatcherService::get()->dispatch(new LoggedInUserEvent($username ?? "N/A", "failed"));
                Exceptions::error_rep("Login failed for username '$username' - no data supplied. Redirecting...");
                die(header("Location: http://{$ini["general"]["base_url"]}/suite/login.php?error=nodata"));
            } else {
                $sql = "SELECT * FROM users WHERE username = ?;";
                $res = $db->sendQuery($sql);
                $res->execute([$username]);

                if($res == false){
                   EventDispatcherService::get()->dispatch(new LoggedInUserEvent($username, "failed"));
                   Exceptions::error_rep("Login failed for username '$username' - Database connection error. Redirecting...");
                   die(header("Location: http://{$ini["general"]["base_url"]}/suite/login.php?error=nodata"));
                }

                $count = $res->rowCount();
                if($count != 1){
                    EventDispatcherService::get()->dispatch(new LoggedInUserEvent($username, "failed"));
                    Exceptions::error_rep("Login failed for username '$username' - User not found. Redirecting...");
                    die(header("Location: http://{$ini["general"]["base_url"]}/suite/login.php?error=nodata"));
                }

                $data = $res->fetch(\PDO::FETCH_ASSOC);

                # check login for ldap
                if(@$ldap == true){
                    Exceptions::error_rep("Successfully authenticated user '" . $username . "' - LDAP Auth");
                    $ts = time();
                    $_SESSION["logged_in"] = true;
                    $_SESSION["username"] = $username;
                    $_SESSION["time"] = date("d.m.Y H:i:s", $ts);
                    self::store_state($username);

                    if(@isset($option["remember"])){
                        if($ini["general"]["app"] == "true"){
                            EventDispatcherService::get()->dispatch(new LoggedInUserEvent($username, "success"));
                            Exceptions::error_rep("Successfully authenticated user '" . $username . "' - LDAP Auth");
                            @ini_set("session.cookie_samesite", "None");
                            @session_set_cookie_params(["path" => "/", "domain" => $ini["general"]["base_url"], "secure" => true, "samesite" => "None"]);
                            setcookie("erinnern", "true", ["samesite" => "None", "secure" => true, "domain" => $ini["general"]["base_url"], "expires" => $ts + (60*60*24*30), "path" => "/"]);
                            setcookie("username", $username, ["samesite" => "None", "secure" => true, "domain" => $ini["general"]["base_url"], "expires" => $ts + (60*60*24*30), "path" => "/"]);
                            header("Refresh: 1; url=http://{$ini["general"]["base_url"]}/suite");
                        } else {
                            EventDispatcherService::get()->dispatch(new LoggedInUserEvent($username, "success"));
                            Exceptions::error_rep("Successfully authenticated user '" . $username . "' - LDAP Auth");
                            setcookie("erinnern", "true", $ts+(60*60*24*30), "/");
                            setcookie("username", $username, $ts + (60*60*24*30), "/");
                            header("Refresh: 1; url=http://{$ini["general"]["base_url"]}/suite");
                        }
                    } else {
                        EventDispatcherService::get()->dispatch(new LoggedInUserEvent($username, "success"));
                        Exceptions::error_rep("Successfully authenticated user '" . $username . "' - LDAP Auth");
                        header("Refresh: 1; url=http://{$ini["general"]["base_url"]}/suite");
                    }
                    die();
                }
                if(isset($option["nfclogin"])){
                    goto nfclogin;
                }
                if(password_verify($password, $data["password"])){
                    if($option["nfclogin"]){
                        nfclogin:
                        Exceptions::error_rep("Authenticated user via NFC login '" . $username . "'");
                    }
                    Exceptions::error_rep("Successfully authenticated user '" . $username . "'");
                    $ini = Arbeitszeit::get_app_ini();
                    $ts = time();
                    $_SESSION["logged_in"] = true;
                    $_SESSION["username"] = $username;
                    $_SESSION["time"] = date("d.m.Y H:i:s", $ts);
                    $_SESSION["provider"] = "(local)";
                    self::store_state($username);

                    if(@isset($option["remember"])){
                        if($ini["general"]["app"] == "true"){
                            EventDispatcherService::get()->dispatch(new LoggedInUserEvent($username, "success"));
                            Exceptions::error_rep("Successfully authenticated user '" . $username . "'");
                            @ini_set("session.cookie_samesite", "None");
                            @session_set_cookie_params(["path" => "/", "domain" => $ini["general"]["base_url"], "secure" => true, "samesite" => "None"]);
                            setcookie("erinnern", "true", ["samesite" => "None", "secure" => true, "domain" => $ini["general"]["base_url"], "expires" => $ts + (60*60*24*30), "path" => "/"]);
                            setcookie("username", $username, ["samesite" => "None", "secure" => true, "domain" => $ini["general"]["base_url"], "expires" => $ts + (60*60*24*30), "path" => "/"]);
                            header("Refresh: 1; url=http://{$ini["general"]["base_url"]}/suite");
                        } else {
                            EventDispatcherService::get()->dispatch(new LoggedInUserEvent($username, "success"));
                            Exceptions::error_rep("Successfully authenticated user '" . $username . "'");
                            setcookie("erinnern", "true", $ts+(60*60*24*30), "/");
                            setcookie("username", $username, $ts + (60*60*24*30), "/");
                            header("Refresh: 1; url=http://{$ini["general"]["base_url"]}/suite");
                        }
                    } else {
                        EventDispatcherService::get()->dispatch(new LoggedInUserEvent($username, "success"));
                        Exceptions::error_rep("Successfully authenticated user '" . $username . "'");
                        header("Refresh: 1; url=http://{$ini["general"]["base_url"]}/suite");
                    }
                } else {
                    EventDispatcherService::get()->dispatch(new LoggedInUserEvent($username, "failed"));
                    Exceptions::error_rep("Login failed for username '$username' - Incorrect credentials. Redirecting...");
                    die(header("Location: http://{$base_url}/suite/login.php?error=wrongdata"));
                }
            }
        }

        public function login_validation(){
            Exceptions::error_rep("Validating login...");
            $ini = Arbeitszeit::get_app_ini();
            $baseurl = $ini["general"]["base_url"];
            if($ini["general"]["app"] == "true"){
                @ini_set("session.cookie_samesite", "None");
                header('P3P: CP="CAO PSA OUR"');
                @session_set_cookie_params(["path" => "/", "domain" => $ini["general"]["base_url"], "secure" => true, "samesite" => "None"]);
            }
            @session_start();
            if(isset($_SESSION["logged_in"]) == false){
                EventDispatcherService::get()->dispatch(new ValidatedLoginEvent($_SESSION["username"] ?? "N/A", "failed"));
                Exceptions::error_rep("User not logged in. Redirecting...");
                header("Location: http://{$baseurl}/suite/login.php?error=notloggedin");
            }
            if($this->get_state($_SESSION["username"]) != $_COOKIE["state"]){
                EventDispatcherService::get()->dispatch(new ValidatedLoginEvent($_SESSION["username"] ?? "N/A", "failed"));
                $this->remove_state($_SESSION["username"]);
                Exceptions::error_rep("State mismatch on user {$_SESSION["username"]}. Removing state and redirecting...");
                header("Location: http://{$baseurl}/suite/login.php?error=statemismatch");
            }
        }

        /**
         * logout() - Logs out user
         * 
         * @return bool Returns true on success
         */
        public function logout(){
            EventDispatcherService::get()->dispatch(new LoggedOutUserEvent($_SESSION["username"]));
            session_start();
            session_unset();
            session_destroy();
            return true;
        }

        /**
         * reset_password() - Resets the user's password
         * 
         * @param int $id The ID of the user resetting his password
         * @return bool Returns true on success, false otherwise
         */
        public function reset_password($username){
            $this->mails()->init(new PHPMailerMailsProvider($this, $username, true));
            if($this->mails()->sendMail("PasswordResetTemplate", ["username" => $username, "email" => $this->benutzer()->get_user($username)["email"]]) == 1){
                return true;
            } else {
                return false;
            }
        }

        /**
         * store_state() - Stores the state of a user in the database
         * 
         * @param string $user Username
         * @return string Returns the state
         */
        public static function store_state($user){
            $db = new DB;
            $ini = self::get_app_ini();
            $state = bin2hex(random_bytes(12));
            if($ini["general"]["app"] == "true"){
                @ini_set("session.cookie_samesite", "None");
                @session_set_cookie_params(["path" => "/", "domain" => $ini["general"]["base_url"], "secure" => true, "samesite" => "None"]);
                setcookie("state", $state, null, "/");
                session_regenerate_id(true);
            } else {
                setcookie("state", $state, null, "/");
                $_SESSION["state"] = $state;
            }
            $sql = "UPDATE `users` SET `state` = ? WHERE `username` = ?;";
            $res = $db->sendQuery($sql)->execute([$state, $user]);
            if($res == false){
                return false;
            } else {
                return $state;
            }
        }

        public function get_state($user){
            $sql = "SELECT state FROM users WHERE username = ?;";
            $res = $this->db->sendQuery($sql);
            $res->execute([$user]);
            $count = $res->rowCount();
            if($count == 0 || $count > 1){
                return false;
            } else {
                return $res->fetch(\PDO::FETCH_ASSOC)["state"];
            }
        }

        public function remove_state($user){
            $sql = "UPDATE `users` SET `state` = '' WHERE `username` = ?;";
            $res = $this->db->sendQuery($sql)->execute([$user]);
            if($res == false){
                return false;
            } else {
                return true;
            }
        }

        public function mail_init($user, $html = false){
            $ini = Arbeitszeit::get_app_ini();
            $mail = new PHPMailer(true);
            try {
                $userdata = Benutzer::get_user($user);
                #$mail->SMTPDebug = SMTP::DEBUG_SERVER;
                $mail->isSMTP();
                $mail->Host = $ini["smtp"]["host"];
                $mail->SMTPAuth = true;
                $mail->Username = $ini["smtp"]["username"];
                $mail->Password = $ini["smtp"]["password"];
                if($ini["smtp"]["usessl"] == true || $ini["smtp"]["usessl"] == "true"){
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                } else {
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                }
                $mail->Port = $ini["smtp"]["port"];
                $mail->setFrom($ini["smtp"]["username"], "TimeTrack");
                $r = $mail->addAddress($userdata["email"], $userdata["name"]);
                if(!$r){
                    Exceptions::error_rep("Could not set address! | Email:" . $userdata["email"] . " - Username: " . $userdata["username"]);
                    return false;
                }
                

                if($html == true){
                    $mail->isHTML(true);
                }

            } catch (Exception $e){
                Exceptions::error_rep($e);
                Exceptions::error_rep("Error while initiating mails object! | Email:" . $userdata["email"] . " - Username: " . $userdata["username"]);
                return false;

            }
            return $mail;
        }

        public function isPasswordReset(){
            if(@$_POST["reset"] == true && @isset($_POST["email"])){
                $db = new DB;
                $sql = "SELECT * FROM users WHERE email = ?";
                $query = $db->sendQuery($sql);
                $query->execute([$_POST["email"]]);
                @$count = $query->rowCount();
                if($count == 0 || $count > 1){
                    echo "The user does not exist, please re-check the input values!";
                } else {
                    $id = $query->fetch(\PDO::FETCH_ASSOC)["username"];
                    if($this->reset_password($id) == true){
                        echo "An email to reset your password has been sent to your email address.";
                    } else {
                        echo "Could not send an email to your account. Please contact the system administrator!";
                    }
                }
            }
        }
    }
}




?>