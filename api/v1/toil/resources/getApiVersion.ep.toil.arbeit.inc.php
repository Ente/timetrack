<?php
namespace Toil {
    require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
    use Toil\EP;
    use Arbeitszeit\Arbeitszeit;
    use Arbeitszeit\Exceptions;
    use Arbeitszeit\Auth;

    class getApiVersion implements EPInterface
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
            header('Content-Type: application/json');
            $v = file_get_contents(dirname(__DIR__) . "/VERSION");
            echo json_encode(array("version" => $v));
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