<?php
namespace Toil {
    require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
    require_once $_SERVER["DOCUMENT_ROOT"] . "/vendor/autoload.php";
    require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/class/plugins/loader.plugins.arbeit.inc.php";
    require_once dirname(__DIR__, 2) . "/src/Main.php";

    use Toil\EP;
    use Arbeitszeit\Arbeitszeit;
    use NFClogin\NFClogin;
    use Arbeitszeit\Benutzer;

    class writeNfc implements EPInterface
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
                $user = Benutzer::get_user($_GET["username"]);
                $data = $nfc->assignCard($user["id"]);
                echo json_encode($data);
            } catch (\Exception $e) {
                echo json_encode(["error" => true, "message" => $e->getMessage()]);
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
