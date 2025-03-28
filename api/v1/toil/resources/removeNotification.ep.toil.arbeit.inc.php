<?php
namespace Toil {
    require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
    use Toil\EP;
    use Arbeitszeit\Arbeitszeit;
    use Arbeitszeit\Exceptions;
    use Arbeitszeit\Auth;

    class autoremoveNotifications implements EPInterface
    {
        private $arbeit; 

        public function __construct()
        {
            $this->arbeit = new Arbeitszeit;
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
            //check if GET id is set
            if(!isset($_GET["id"])){
                header("Content-Type: application/json");
                echo json_encode(["status" => "Id is missing."]);
                die();
            }

            if($this->arbeit->notifications()->delete_notifications_entry($_GET["id"])){
                header("Content-Type: application/json");
                echo json_encode(["status" => "success"]);
                die();
            } else {
                header("Content-Type: application/json");
                echo json_encode(["status" => "error"]);
            }
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