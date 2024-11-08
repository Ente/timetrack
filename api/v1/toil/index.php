<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/toil/toil.arbeit.inc.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/toil/Routes.toil.arbeit.inc.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/toil/Controller.toil.arbeit.inc.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/toil/resources/ep.toil.arbeit.inc.php";
use Arbeitszeit\Exceptions;
use Toil\Toil;
use Pecee\SimpleRouter\SimpleRouter as Router;
try{ 
new Toil();
$debug = Router::startDebug();
} catch(Exception $e){
    function non($name = null){
        Exceptions::error_rep("[API] Failed authentication for Toil API for user '$name'");
        header("WWW-Authenticate: Basic realm='Toil API'");
        header("HTTP/1.0 404 Not found");
        die("Not found - Toil API");
    }
    non();
}

?>