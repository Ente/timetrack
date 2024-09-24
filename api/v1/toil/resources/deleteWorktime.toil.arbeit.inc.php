<?php
namespace Toil {
    require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
    use Toil\EP;
    use Arbeitszeit\Arbeitszeit;
    use Arbeitszeit\Benutzer;

    class deleteWorktime implements EPInterface
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
            header("Content-Type: application/json");
            $id = $_GET["id"] ?? false;
            if(!$id){
                echo json_encode(["error" => true]);
                die();
            }
            
            $result = $this->arbeit->delete_worktime($id);
            if($result) echo json_encode(["success" => true]) && die();
            echo json_encode(["error" => "could not delete worktime"]); die();
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