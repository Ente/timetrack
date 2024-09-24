<?php
namespace Toil {
    require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
    use Toil\EP;
    use Arbeitszeit\Arbeitszeit;
    use Arbeitszeit\Exceptions;
    use Arbeitszeit\Auth;

    class getOwnWorktime implements EPInterface
    {
        public function __construct()
        {

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
            $ab = new Arbeitszeit;
            $data = $ab->get_all_user_worktime($_SERVER["PHP_AUTH_USER"]);

            if($data === false){
                header("Content-type: application/json");
                echo json_encode(["info" => "no data"]);
                die();
            }

            header("Content-type: application/json");
            echo json_encode($data);
            
        }

        public function post($post = null)
        {

        }

        public function delete()
        {

        }

        public function put()
        {

        }
    }
}


?>