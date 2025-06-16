<?php
namespace Toil {
    require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
    use Toil\EP;
    use Arbeitszeit\Arbeitszeit;
    use Arbeitszeit\Auth;
    use Arbeitszeit\Benutzer;
    use Arbeitszeit\Vacation;

    class addVacation implements EPInterface
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
            $vac = new Vacation;
            $auth->login_validation();
            $start = $_GET["start"];
            $stop = $_GET["stop"];
            $username = $_GET["username"];

            if ($user->is_admin($user->get_user($username)) == true) {
                if ($vac->add_vacation($start, $stop, $username)) {
                    echo json_encode(["note" => "Successfully saved vacation record"]);
                } else {
                    echo json_encode(["error" => "An error occurred while saving vacation"]);
                }
            } else {
                echo json_encode(["error" => "No permission."]);
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
