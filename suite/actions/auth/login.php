<?php
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
use Arbeitszeit\Auth;
$auth = new Auth();

$auth->login($_POST["username"], $_POST["password"], $option = ["erinnern" => $_POST["erinnern"]]);



?>