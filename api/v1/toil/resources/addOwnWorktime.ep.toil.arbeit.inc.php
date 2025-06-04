<?php
namespace Toil {
    require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
    use Toil\EP;
    use Arbeitszeit\Arbeitszeit;
    use Arbeitszeit\Auth;
    use Arbeitszeit\Benutzer;

    class addOwnWorktime implements EPInterface
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
            $auth->login_validation();
            $data = [
                "start" => $_GET["start"],
                "end" => $_GET["end"],
                "date" => $_GET["date"],
                "location" => $_GET["location"],
                "username" => $_SERVER["PHP_AUTH_USER"],
                "type" => "worktime",
                "pause" => [
                    "start" => $_GET["pause_start"] ?? null,
                    "end" => $_GET["pause_end"] ?? null
                ],
                "meta" => $_GET["meta"] ?? null
            ];

            
                if ($arbeit->add_worktime($data["start"], $data["end"], $data["location"], $data["date"], $data["username"], $data["type"], 0, $data["pause"], $data["meta"])) {
                    echo json_encode(["note" => "Successfully saved worktime record"]);
                } else {
                    echo json_encode(["error" => "An error occured while saving worktime"]);
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
