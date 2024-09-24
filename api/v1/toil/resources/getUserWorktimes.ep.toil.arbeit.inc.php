<?php
namespace Toil {
    require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
    use Toil\EP;
    use Arbeitszeit\Arbeitszeit;
    use Arbeitszeit\Benutzer;

    class getUserWorktimes implements EPInterface
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
            $benutzer = new Benutzer;
            header("Content-Type: application/json");
            $username = $_GET["username"] ?? false;
            if(!$username){
                echo json_encode(["error" => true]);
                die();
            }
            
            $data = $this->arbeit->get_all_user_worktime($username);
            if(!$data) echo json_encode(["error" => "no data"]) && die();
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