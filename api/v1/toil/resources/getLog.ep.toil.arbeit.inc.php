<?php
namespace Toil {
    require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
    use Toil\EP;
    use Arbeitszeit\Arbeitszeit;
    use Arbeitszeit\Exceptions;
    use Arbeitszeit\Auth;

    class getLog implements EPInterface
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
            if(isset($_GET["date"])){
                $date = basename($_GET["date"]);
                header('Content-Type: text/plain');
                Exceptions::error_rep("Accessing error file '$date'");
                echo @file_get_contents(Exceptions::getSpecificLogFilePath($date));
            } else {
                header('Content-Type: text/plain');
                $log_contents = @file_get_contents(Exceptions::getSpecificLogFilePath()) ?? "Error retrieving log file!";
                echo $log_contents;
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