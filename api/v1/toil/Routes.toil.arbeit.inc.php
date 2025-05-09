<?php
namespace Toil;

require_once dirname(__DIR__, 3) . "/api/v1/inc/arbeit.inc.php";
use Pecee\SimpleRouter\SimpleRouter as Router;
use Pecee\SimpleRouter\Handlers\EventHandler;
use Pecee\SimpleRouter\Route\IGroupRoute;
use Pecee\SimpleRouter\Route\ILoadableRoute;
use Pecee\SimpleRouter\Event\EventArgument;
use Pecee\Http\Request;
use Pecee\SimpleRouter\Exceptions\NotFoundHttpException;
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Benutzer;
use Arbeitszeit\Exceptions;
use Toil\Controller;
use Toil\Permissions;
use Toil\Tokens;

class Routes extends Toil
{
    private Arbeitszeit $arbeitszeit;
    private Benutzer $benutzer;

    private string $api_username = "";

    private $api_password;

    private $basepath = "/api/v1/toil";

    private EventHandler $eventHandler;

    private $bString = "Toil API";

    public function __construct()
    {
        $this->__set("eventHandler", new EventHandler());
        $this->__set("arbeitszeit", new Arbeitszeit());
        $this->__set("benutzer", new Benutzer());

        $endpoint = preg_replace('/\?.*/', '', $_SERVER['REQUEST_URI']);
        $endpointName = $this->getResourceNameFromPath($endpoint);
        $permissions = new Permissions();

        if ($permissions->checkPermissions(null, $endpointName)) {
            Exceptions::error_rep("[API] Public access to '$endpointName' allowed – skipping authentication.");
            return;
        }
        $user = $_SERVER["PHP_AUTH_USER"] ?? null;
        $pw = $_SERVER["PHP_AUTH_PW"] ?? null;
        $tokenAuth = false;
        if (isset($_SERVER["HTTP_AUTHORIZATION"])) {
            $authHeader = $_SERVER["HTTP_AUTHORIZATION"];
            if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
                $token = $matches[1];
                $tokens = new Tokens();
                if ($tokens->validate_token($token)) {
                    Exceptions::error_rep("[API] Token is valid.");
                    $t = $tokens->get_token_from_token($token);
                    $user = $this->arbeitszeit->benutzer()->get_user_from_id($t["user_id"])["username"];
                    $pw = $t["access_token"];
                    $tokenAuth = true;
                    
                } else {
                    Exceptions::error_rep("[API] Token is invalid.");
                    header("Content-type: application/json");
                    header("HTTP/1.1 401 Unauthorized");
                    echo json_encode(["error" => "invalid token"]);
                    die();
                }
            }
        }


        $this->__set("arbeitszeit", new Arbeitszeit());
        $this->__set("benutzer", new Benutzer());
        if (!$user) {
            Exceptions::error_rep("[API] No username provided.");
            $this->authError("No username provided.");
        } else {
            $this->__set("api_username", $user);
        }

        $this->__set("api_password", $pw ?? $this->authError($user));

        if($tokenAuth == true){
            Exceptions::error_rep("[API] Token authentication successful.");
            return;
        }

        if (!$this->login(username: $user, password: $pw)) {
            $this->authError($user);
        }
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function authError($name = null)
    {
        Exceptions::error_rep("[API] Failed authentication for Toil API for user '{$name}'");
        header("WWW-Authenticate: Basic realm='" . $this->bString . "'");
        header("HTTP/1.0 401 Unauthorized");
        die("Not authenticated");
    }

    public function login($username, $password)
    {
        if ($this->__get("benutzer")->get_user($username)) {
            $data = $this->__get("benutzer")->get_user($username);
            if (password_verify($password, $data["password"])) {
                Exceptions::error_rep("[API] Successfully authenticated user '$username' via API.");
                return true;
            }
            Exceptions::error_rep("[API] Failed authenticating user '$username' via API.");
            return false;
        }
    }

    public function routing($eventHandler)
    {
        Exceptions::error_rep("[API] Start API routing request and registering routes...");
        $base = $this->__get("basepath");
        $user = $this->__get("api_username");
        $eventHandler->register(EventHandler::EVENT_ADD_ROUTE, function (EventArgument $event) use ($base) {
            $route = $event->route;
            if (!$event->isSubRoute) {
                return;
            }
            switch (true) {
                case $route instanceof ILoadableRoute:
                    $route->prependUrl($base);
                    break;
                case $route instanceof IGroupRoute:
                    $route->prependPrefix($base);
                    break;
            }
        });


        # Before letting user accessing the API endpoint, checking if authorized

        $permissions = new Permissions;
        preg_match("/\/([^\/?]+)(\?.*)?$/m", $_SERVER["REQUEST_URI"], $matches);
        if (!$permissions->checkPermissions($user, $matches[1])) {
            Exceptions::error_rep("[API] Failed checking permissions for expected endpoint: " . $matches[1]);
            header("Content-type: application/json");
            header("HTTP/1.1 403 Forbidden");
            echo json_encode(["error" => "forbidden"]);
            die();
        }

        Router::addEventHandler($eventHandler);
        Router::get("/api/v1/toil/getVersion", function () {
            Exceptions::error_rep("Accessing 'getVersion' endpoint. User: " . $this->__get("api_username"));
            Controller::createview("getVersion");
        });
        Router::get("/api/v1/toil/healthcheck", function () {
            Exceptions::error_rep("[API] User authenticated and accessing 'healthcheck' endpoint");
            Controller::createview("healthcheck");
        });
        Router::get("/api/v1/toil/getUserCount", function () {
            Exceptions::error_rep("[API] User authenticated and acceessing 'getUserCount' endpoint");
            Controller::createview("getUserCount");
        });
        Router::get("/api/v1/toil/getApiVersion", function () {
            Exceptions::error_rep("[API] User authenticated and accessing 'getApiVersion' endpoint");
            Controller::createview("getApiVersion");
        });
        Router::get("/api/v1/toil/getLog", function () {
            Exceptions::error_rep("[API] User authenticated and accessing 'getLog' endpoint");
            Controller::createview("getLog");
        });
        Router::get("/api/v1/toil/getWorktimes", function () {
            Exceptions::error_rep("[API] User authenticated and accessing 'getWorktimes' endpoint");
            Controller::createview("getWorktimes");
        });

        Router::get("/api/v1/toil/getVacations", function () {
            Exceptions::error_rep("[API] User authenticated and accessing 'getVacations' endpoint");
            Controller::createview("getVacations");
        });

        Router::get("/api/v1/toil/getUsers", function () {
            Exceptions::error_rep("[API] User authenticated and accessing 'getUsers' endpoint");
            Controller::createview("getUsers");
        });

        Router::get("/api/v1/toil/getUserDetails", function () {
            Exceptions::error_rep("[API] User authenticated and accessing 'getUserDetails' endpoint");
            Controller::createview("getUserDetails");
        });

        Router::get("/api/v1/toil/approveVacation", function () {
            Exceptions::error_rep("[API] User authenticated and accessing 'approveVacation' endpoint");
            Controller::createview("approveVacation");
        });

        Router::get("/api/v1/toil/addWorktime", function () {
            Exceptions::error_rep("[API] User authenticated and accessing 'addWorktime' endpoint");
            Controller::createview("addWorktime");
        });

        Router::get("/api/v1/toil/addVacation", function () {
            Exceptions::error_rep("[API] User authenticated and accessing 'addVacation' endpoint");
            Controller::createview("addVacation");
        });

        Router::get("/api/v1/toil/addProject", function () {
            Exceptions::error_rep("[API] User authenticated and accessing 'addProject' endpoint");
            Controller::createview("addProject");
        });

        Router::get("/api/v1/toil/getUserWorktimes", function () {
            Exceptions::error_rep("[API] User authenticated and accessing 'getUserWorktimes' endpoint");
            Controller::createview("getUserWorktimes");
        });

        Router::get("/api/v1/toil/deleteWorktime", function () {
            Exceptions::error_rep("[API] User authenticated and accessing 'deleteWorktime' endpoint");
            Controller::createview("deleteWorktime");
        });

        Router::get("/api/v1/toil/deleteUser", function () {
            Exceptions::error_rep("[API] User authenticated and accessing 'deleteUser' endpoint");
            Controller::createview("deleteUser");
        });

        Router::get("/api/v1/toil/addUser", function () {
            Exceptions::error_rep("[API] User authenticated and accessing 'addUser' endpoint");
            Controller::createview("addUser");
        });
        Router::get("/api/v1/toil/getOwnWorktime", function () {
            Exceptions::error_rep("[API] User authenticated and accessing 'getOwnWorktime' endpoint");
            Controller::createview("getOwnWorktime");
        });
        Router::get(("/api/v1/toil/addNotification"), function(){
            Exceptions::error_rep("[API] User authenticated and accessing 'addNotification' endpoint");
            Controller::createview("addNotification");
        });
        Router::get("/api/v1/toil/removeNotification", function(){
            Exceptions::error_rep("[API] User authenticated and accessing 'removeNotification' endpoint");
            Controller::createview("removeNotification");
        });
        Router::get("/api/v1/toil/getNotifications", function(){
            Exceptions::error_rep("[API] User authenticated and accessing 'getNotifications' endpoint");
            Controller::createview("getNotifications");
        });
        Router::get("/api/v1/toil/autoremoveNotifications", function(){
            Exceptions::error_rep("[API] User authenticated and accessing 'autoremoveNotifications' endpoint");
            Controller::createview("autoremoveNotifications");
        });

        Router::get("/api/v1/toil/createToken", function () {
            Exceptions::error_rep("[API] User authenticated and accessing 'createToken' endpoint");
            Controller::createview("createToken");
        });

        Router::get("/api/v1/toil/deleteToken", function () {
            Exceptions::error_rep("[API] User authenticated and accessing 'deleteToken' endpoint");
            Controller::createview("deleteToken");
        });

        Router::post("/api/v1/toil/refreshToken", function () {
            Exceptions::error_rep("[API] User authenticated and accessing 'refreshToken' endpoint");
            Controller::createview("refreshToken");
        });

        // Loading all custom routes
        CustomRoutes::loadCustomRoutes();

        Router::error(function (Request $request, \Exception $e) {
            switch ($e->getCode()) {
                case 404:
                    Exceptions::error_rep("[API] 404 Not found. Endpoint: " . $request->getUrl());
                    header("Content-type: application/json");
                    header("HTTP/1.1 404 Not found");
                    echo json_encode(["error" => "not found"]);
                    die();
                case 403:
                    Exceptions::error_rep("[API] 403 Forbidden. Endpoint: " . $request->getUrl());
                    header("Content-type: application/json");
                    header("HTTP/1.1 403 Forbidden");
                    echo json_encode(["error" => "forbidden"]);
                    die();
            }
        });
    }

    public function getResourceNameFromPath($path)
    {
        preg_match("/[^\/]+$/", $path, $match);
        return $match[0];
    }

}


?>