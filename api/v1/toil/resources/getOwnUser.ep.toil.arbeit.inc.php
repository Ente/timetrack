<?php
namespace Toil {
    require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
    use Toil\EP;
    use Arbeitszeit\Arbeitszeit;
    use Arbeitszeit\Benutzer;

    class getOwnUser implements EPInterface
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
            $user = $_SERVER["PHP_AUTH_USER"];

            $data = $this->arbeitszeit->benutzer()->get_user($user);

            unset($data["password"]);
            unset($data["state"]);
            unset($data[4], $data[7]);

            if($data == false){
                $r = [
                    "error" => "An error occured while fetching user data"
                ];
                echo json_encode($r);
                die();
            }

            $r = $data;
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