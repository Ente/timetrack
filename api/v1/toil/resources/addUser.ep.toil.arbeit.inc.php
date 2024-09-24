<?php
namespace Toil {
    require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
    use Toil\EP;
    use Arbeitszeit\Arbeitszeit;
    use Arbeitszeit\Benutzer;

    class addUser implements EPInterface
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
            $result = $this->arbeit->benutzer()->create_user($_GET["username"], $_GET["name"], $_GET["email"], $_GET["password"], (bool)$_GET["isAdmin"]);
            echo json_encode(["result" => $result]);
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