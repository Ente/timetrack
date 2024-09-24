<?php
namespace Toil {
    require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
    use Toil\EP;
    use Arbeitszeit\Arbeitszeit;
    use Arbeitszeit\Vacation;

    class getVacations implements EPInterface
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
            $vacationdat = $vacation->get_all_vacation();
            if ($vacationdat != false) {
                echo json_encode($vacationdat);
            } else {
                echo json_encode(["error" => true]);
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