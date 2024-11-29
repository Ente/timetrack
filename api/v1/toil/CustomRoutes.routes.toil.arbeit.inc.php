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

        public static function registerCustomRoute($endpoint, $classFile, $permissions = 0){
            $file = json_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/data/routes/routes.json"), true);
            $file[$endpoint] = $classFile;
            file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/data/routes/routes.json", json_encode($file, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            $permissionsFile = json_decode(file_get_contents(__DIR__ . "/permissions.json"), true);
            $permissionsFile[$endpoint] = $permissions;
            file_put_contents(__DIR__ . "/permissions.json", json_encode($permissionsFile, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            
            return true;
        }

        public static function removeCustomRoute($endpoint){
            $file = json_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/data/routes/routes.json"), true);
            unset($file->$endpoint);
            file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/data/routes/routes.json", json_encode($file, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            $permissionsFile = json_decode(file_get_contents(__DIR__ . "/permissions.json"), true);
            unset($permissionsFile->$endpoint);
            file_put_contents(__DIR__ . "/permissions.json", json_encode($permissionsFile, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            
            return true;
        }

        public static function loadCustomRoute($endpoint, $classFile, $method = "GET"){
            Router::get("/api/v1/toil/" . $endpoint, function() use ($endpoint, $classFile){
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
