<?php
namespace Toil {
    require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
    use Toil\EP;
    use Arbeitszeit\Arbeitszeit;

    class getWorktimes implements EPInterface
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
            $arbeit = new Arbeitszeit;
            header('Content-Type: application/json');
            $worktimes = $arbeit->get_all_worktime();
            if ($worktimes != false) {
                echo json_encode($worktimes);
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