<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
use Arbeitszeit\Benutzer;
$benutzer = new Benutzer;
header("Content-Type: application/json");
$username = $_GET["username"] ?? false;
if(!$username){
    echo json_encode(["error" => true]);
    die();
}

$user = $benutzer->get_user($username);
if($user == false){
    echo json_encode(["error" => "User not found"]);
    die();
}
unset($user["password"]);
unset($user["state"]);

echo json_encode($user);
