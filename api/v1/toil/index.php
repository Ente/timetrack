<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/toil/Routes.php";
use Arbeitszeit\Exceptions;
use Pecee\SimpleRouter\SimpleRouter as Router;
try{ 
Router::start();
$debug = Router::startDebug();
} catch(Exception $e){
    function non($name = null){
        Exceptions::error_rep("[LIC] Failed authentication for Toil API for user '$name'");
        header("WWW-Authenticate: Basic realm='Toil API v1.4'");
        header("HTTP/1.0 404 Not found");
        die("Not found - Toil API v1.4");
    }
    non();
}

?>