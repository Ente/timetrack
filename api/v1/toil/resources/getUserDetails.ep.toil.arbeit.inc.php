<?php
namespace Toil {
    require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
    use Toil\EP;
    use Arbeitszeit\Arbeitszeit;
    use Arbeitszeit\Benutzer;

    class getUserDetails implements EPInterface
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
            $benutzer = new Benutzer;
            header("Content-Type: application/json");
            $username = $_GET["username"] ?? false;
            if(!$username){
                echo json_encode(["error" => true]);
                die();
            }
            
            $user = $benutzer->get_user($username);
            if($user == false){
                echo json_encode(["error" => "User not found"]);
                die();
            }
            unset($user["password"]);
            unset($user["state"]);
            unset($user[4], $user[7]);
            echo json_encode($user);
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