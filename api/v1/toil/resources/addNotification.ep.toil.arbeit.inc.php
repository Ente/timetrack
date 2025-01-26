<?php
namespace Toil {
    require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
    use Toil\EP;
    use Arbeitszeit\Arbeitszeit;
    use Arbeitszeit\Exceptions;
    use Arbeitszeit\Auth;

    class addNotification implements EPInterface
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

            if(!isset($_GET["comment"]) || !isset($_GET["date"]) || !isset($_GET["time"]) || !isset($_GET["location"])){
                header("Content-Type: application/json");
                echo json_encode(["status" => "One or more parameters are missing."]);
                die();
            }

            $configuration = array(
                "comment" => $_GET["comment"],
                "date" => $_GET["date"],
                "time" => $_GET["time"],
                "location" => $_GET["location"],
            );

            if($this->arbeit->notifications()->create_notifications_entry($configuration["time"], $configuration["date"], $configuration["location"], $configuration["comment"])){
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