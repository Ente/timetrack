<?php
namespace Toil {
    require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
    require_once $_SERVER["DOCUMENT_ROOT"] . "/vendor/autoload.php";
    require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/class/plugins/loader.plugins.arbeit.inc.php";
    require_once dirname(__DIR__, 2) . "/src/Main.php";

    use Toil\EP;
    use Arbeitszeit\Arbeitszeit;
    use Arbeitszeit\Exceptions;
    use Arbeitszeit\Benutzer;
    use NFClogin\NFClogin;

    class readBlock4 implements EPInterface
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
                $data = $nfc->readBlock4();
                if(isset($data["value"])) {
                    $data["username"] = Benutzer::get_user_from_id($data["value"])["username"] ?? null;
                }
                echo json_encode($data);
            } catch (\Exception $e) {
                Exceptions::error_rep("An error occured while reading authentication block 4: " . $e->getMessage());
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
