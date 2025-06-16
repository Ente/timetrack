<?php
namespace Toil {
    require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
    require_once $_SERVER["DOCUMENT_ROOT"] . "/vendor/autoload.php";
    require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/class/plugins/loader.plugins.arbeit.inc.php";
    require_once dirname(__DIR__, 2) . "/src/Main.php";

    use Toil\EP;
    use Arbeitszeit\Arbeitszeit;
    use Arbeitszeit\StatusMessages;
    use Arbeitszeit\Exceptions;
    use Arbeitszeit\Benutzer;
    use Arbeitszeit\Auth;
    use NFClogin\NFClogin;

    class nfcclogin implements EPInterface
    {
        public function __construct()
        {
            // Required by interface
        }

        public function __set($name, $value)
        {
            $this->$name = $value;
        }

        public function __get($name)
        {
            return $this->$name;
        }

        public function get()
        {
            header('Content-Type: application/json');

            try {
                $nfc = new NFClogin;
                $block = $nfc->readBlock4();
                $uid = $nfc->readCard()["uid"] ?? null;
                $val = $block["value"];
                $statusMessages = new StatusMessages;
                $getMapUser = $nfc->getUser($uid);
                $dbUser = Benutzer::get_user_from_id($val)["username"] ?? null;
                if($getMapUser == $dbUser){
                    Auth::login($dbUser, "", ["nfclogin" => true]);
                } else {
                    header("Location: /suite/login.php?" . $statusMessages->URIBuilder("wrongdata") . "&uid={$uid}&block={$val}");
                    exit;
                }
                
            } catch (\Exception $e) {
                Exceptions::error_rep("An error occurred while processing the NFC login: " . $e->getMessage());
                echo json_encode(["error" => true, "message" => "An error occured."]);
            }
        }

        public function post($post = null)
        {
            // Optional
        }

        public function delete()
        {
            // Optional
        }

        public function put()
        {
            // Optional
        }
    }
}
