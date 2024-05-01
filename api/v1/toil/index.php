<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/toil/Routes.php";
use Pecee\SimpleRouter\SimpleRouter as Router;

Router::start();
$debug = Router::startDebug();


?>