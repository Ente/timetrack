<?php
namespace Toil {

    use Arbeitszeit\Exceptions;
    use Pecee\SimpleRouter\SimpleRouter as Router;
    use Pecee\SimpleRouter\Handlers\EventHandler;
    use Pecee\SimpleRouter\Route\IGroupRoute;
    use Pecee\SimpleRouter\Route\ILoadableRoute;
    use Pecee\SimpleRouter\Event\EventArgument;
    use Pecee\Http\Request;
    use Pecee\SimpleRouter\Exceptions\NotFoundHttpException;
    class CustomRoutes extends Routes {
        public function __construct(){

        }

        public static function loadCustomRoute($endpoint, $classFile, $method = "GET"){
            Router::get("/api/v1/toil/" . "myCheck", function() use ($endpoint, $classFile){
                Exceptions::error_rep("[API] User authenticated and accessing custom API endpoint '{$endpoint}'");
                Controller::createcustomview($endpoint, $classFile);
            });
        }

        public static function loadCustomRoutes($routeFile = "/data/routes/routes.json"){
            $routes = json_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/" . $routeFile));

            foreach($routes as $route => $class){
                try {
                    if($route == "_comment"){continue;}
                    Exceptions::error_rep("[API] Loading custom API route '{$route}'");
                    self::loadCustomRoute($route, $class);
                } catch (\Throwable $e){
                    Exceptions::error_rep("[API] Could not load custom API route. Message: " . $e->getMessage() . " | Code: " . $e->getCode() . " | Trace (if available): " . $e->getTraceAsString());
                }
            }
        }
    }
}


?>