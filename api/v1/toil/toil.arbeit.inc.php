<?php

namespace Toil;
use Arbeitszeit\Exceptions;
    class Toil {
        public function __construct(){
            try {
                $routes = new Routes;
                $routes->routing($routes->__get("eventHandler"));
            } catch (\Exception $e){
                Exceptions::error_rep("An error occurred while accessing the API | Code: " . $e->getCode() . " | Message: " . $e->getMessage() . " | Trace: " . $e->getTraceAsString());
                header("HTTP/1.1 500 Internal Server Error");
                header("Content-type: application/json");
                echo json_encode(array("code" => 500, "message" => "Internal Server error. View log fore more details."));
            }
        }
    }
