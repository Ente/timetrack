<?php
namespace Toil {
    require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
    use Toil\EP;
    use Arbeitszeit\Arbeitszeit;
    use Arbeitszeit\Vacation;

    class approveVacation implements EPInterface
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
            $vacation = new Vacation;
            header('Content-Type: application/json');


            $id = $_GET["id"] ?? die(json_encode(["error" => true]));
            if ($id != null) {
                if ($vacation->change_status($id, 1)) {
                    echo json_encode(["id" => $id]);
                } else {
                    echo json_encode(["error" => true]);
                }
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


?>