<?php
namespace Toil {
    use Toil\Toil;
    class Controller extends Toil {
        public static function createview($resource){
            require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/toil/resources/{$resource}.ep.toil.arbeit.inc.php";
            $class = "Toil\\$resource";
            $class = new $class;

            $method = $_SERVER["REQUEST_METHOD"];
            switch($method){
                case "GET":
                    $class->$method();
                    die();
                case "POST":
                    $class->$method($_POST);
                    die();
            }
            die("No suitable method found.");
        }
    }
}


?>