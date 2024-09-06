<?php
namespace Toil {
    require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
    use Toil\EP;
    use Arbeitszeit\Arbeitszeit;
    use Arbeitszeit\Auth;
    use Arbeitszeit\Benutzer;
    use Arbeitszeit\Projects;

    class addProject implements EPInterface
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
            header("Content-type: application/json");
            $user = new Benutzer;
            $arbeit = new Arbeitszeit;
            $auth = new Auth;
            $projects = new Projects;
            $auth->login_validation();
            $data = [
                "name" => $_GET["name"],
                "description" => $_GET["description"],
                "note" => $_GET["note"],
                "users" => explode(";", $_GET["users"])
            ];

            if ($user->is_admin($user->get_user($_SESSION["username"])) == true) {
                if ($projects->addProjectE($data["name"], $data["description"], $data["note"], $data["users"])) {
                    echo json_encode(["note" => "Successfully saved new project"]);
                } else {
                    echo json_encode(["error" => "An error occured while saving project"]);
                }
            } else {
                echo json_encode(["error" => "No permission."]);
            }
        }

        public function post()
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
