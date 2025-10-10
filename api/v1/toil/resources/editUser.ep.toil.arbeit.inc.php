<?php
namespace Toil {
    require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
    use Toil\EP;
    use Arbeitszeit\Arbeitszeit;
    use Arbeitszeit\Benutzer;

    class editUser implements EPInterface
    {

        private $arbeitzeit;

        public function __construct()
        {
            $this->arbeitzeit = new Arbeitszeit;
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
            $res = $this->arbeitszeit->benutzer()->editUserProperties(
                $_GET["username"],
                $_GET["name"],
                $_GET["value"]
            );

            if($res){
                $r = array(
                    "status" => "success",
                    "username" => $_GET["username"],
                    "name" => $_GET["name"],
                    "value" => $_GET["value"],
                    "updated" => true
                );
            } else {
                $r = array(
                    "status" => "error",
                    "username" => $_GET["username"],
                    "name" => $_GET["name"],
                    "value" => $_GET["value"],
                    "updated" => false,
                    "message" => "Please review the log for additional information"
                );
            }

            echo json_encode($r);
            
        }

        public function post($post = null)
        {
            /**
             * Empty, required by interface
             *
             */
        }

        public function delete()
        {
            /**
             * Empty, required by interface
             *
             */
        }

        public function put()
        {
            /**
             * Empty, required by interface
             *
             */
        }
    }
}


?>