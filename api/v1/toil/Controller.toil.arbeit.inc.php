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

        public static function createcustomview($endpoint, $classFile){
            try {
                @require_once $_SERVER["DOCUMENT_ROOT"] . $classFile;
                $class = "Toil\\$endpoint";
                $class = new $class;
            } catch (\Throwable $e){
                \Arbeitszeit\Exceptions::error_rep("[API] An error occurred while loading custom route for endpoint {$endpoint}: " . $e->getMessage());
                header("Content-Type: application/json");
                echo json_encode(array("error" => "Could not load custom route."));
                die();
            }

            $method = $_SERVER["REQUEST_METHOD"];
            switch($method){
                case "GET":
                    $class->$method();
                    die();
                case "POST":
                    $class->$method($_POST);
                    die();
            }
            \Arbeitszeit\Exceptions::error_rep("[API] An API route exists, but no suitable method was found for endpoint: {$endpoint}");
            die("No suitable method found.");
        }
    }
}


?>