<?php
namespace Toil {
    require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
    use Toil\EP;

    class healthcheck implements EPInterface {
        public function __construct(){

        }

        public function __set($name, $value){
            $this->$name = $value;
        }

        public function __get($name){
            return $this->$name;
        }

        public function get(){
            header('Content-Type: application/json');
            echo json_encode(array("status" => "alive"));
        }

        public function post(){

        }

        public function delete(){

        }

        public function put(){
            
        }
    }
}


?>