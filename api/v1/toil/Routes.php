<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
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
$ar = new Arbeitszeit;
$benutzer = new Benutzer;

# user authentication
(string) @$user = $_SERVER["PHP_AUTH_USER"] or non("No username provided") ?? "N/A";
$pass = $_SERVER["PHP_AUTH_PW"] or non();

function non($name = null){
        Exceptions::error_rep("[LIC] Failed authentication for Toil API for user '$name'");
        header("WWW-Authenticate: Basic realm='Toil API v1.4'");
        header("HTTP/1.0 401 Unauthorized");
        die("Not authenticated - Toil API v1.4");
}

// rework this function to use the native Auth::login function. This messes with other code inside the API endpoints which requires the session parameters to be set
if($benutzer->get_user($user)){
    $data = $benutzer->get_user($user) or non();
    if(password_verify($pass, $data["password"])){
        # route handling
        Exceptions::error_rep("[LIC] Successfully authenticated user '$user'.");
        $basepath = "/api/v1/toil/";
        $eventHandler = new EventHandler();
        $eventHandler->register(EventHandler::EVENT_ADD_ROUTE, function(EventArgument $event) use ($basepath){
            $route = $event->route;
            if(!$event->isSubRoute){
                return;
            }
            switch(true){
                case $route instanceof ILoadableRoute:
                    $route->prependUrl($basepath);
                    break;
                case $route instanceof IGroupRoute:
                    $route->prependPrefix($basepath);
                    break;
            }
        });
        Router::addEventHandler($eventHandler);
        # Routes
        Router::get("/api/v1/toil/getVersion", function(){
            Exceptions::error_rep("[LIC] User '$user' authenticated and accessing 'getVersion' endpoint");
            Controller::createview("getVersion");
        });
        Router::get("/api/v1/toil/healthcheck", function(){
            Exceptions::error_rep("[LIC] User '$user' authenticated and accessing 'healthcheck' endpoint");
            Controller::createview("healthcheck");
        });
        Router::get("/api/v1/toil/getUserCount", function(){
            Exceptions::error_rep("[LIC] User '$user' authenticated and acceessing 'getUserCount' endpoint");
            Controller::createview("getUserCount");
        });
        Router::get("/api/v1/toil/getApiVersion", function(){
            Exceptions::error_rep("[LIC] User '$user' authenticated and accessing 'getApiVersion' endpoint");
            Controller::createview("getApiVersion");
        });
        Router::get("/api/v1/toil/getLog", function(){
            Exceptions::error_rep("[LIC] User '$user' authenticated and accessing 'getLog' endpoint");
            Controller::createview("getLog");
        });
        Router::get("/api/v1/toil/Worktimes", function(){
            Exceptions::error_rep("[LIC] User '$user' authenticated and accessing 'getWorktimes' endpoint");
            Controller::createview("getWorktimes");
        });
    } else {
        non($user);
    }
} else {
    non($user);
}


?>