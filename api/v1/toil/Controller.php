<?php
namespace Toil {
    class Controller {
        public static function createview($resource){
            require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/toil/resources/{$resource}.php";
        }
    }
}


?>