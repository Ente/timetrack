<?php
namespace Toil {
    require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
    use Toil\EP;
    use Arbeitszeit\Arbeitszeit;

    class getVersion implements EPInterface
    {
        public function __construct()
        {
            /**
             * Empty, required by interface
             *
             */
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
            $v = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/VERSION");
            echo json_encode(array("version" => $v));
        }

        public function post($post = null)
        {
            /**
             * Empty, required by interface
             *
             */
        }

        public function delete()
        {
            /**
             * Empty, required by interface
             *
             */
        }

        public function put()
        {
            /**
             * Empty, required by interface
             *
             */
        }
    }
}


?>