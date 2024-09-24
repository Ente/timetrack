<?php
namespace Toil {
    require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
    use Toil\EP;
    use Arbeitszeit\Arbeitszeit;
    use Arbeitszeit\Benutzer;

    class deleteUser implements EPInterface
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
            
            $result = @$this->arbeit->benutzer()->delete_user($id);
            echo json_encode(["result" => $result]); // only returns false or an array if the connection to the db could not be established or something similar but not if the user simply doesn't exist
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