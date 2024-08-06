<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Auth;
use Arbeitszeit\Benutzer;
$user = new Benutzer;
$arbeit = new Arbeitszeit;
$auth = new Auth;
$auth->login_validation();
$data = [
    "start" => $_GET["start"],
    "end" => $_GET["end"],
    "date" => $_GET["date"],
    "location" => $_GET["location"],
    "username" => $_GET["username"],
    "type" => "worktime",
    "pause" => [
        "start" => $_GET["pause_start"] ?? null,
        "end" => $_GET["pause_end"] ?? null
    ],
    "meta" => $_GET["meta"] ?? null
];

if($user->is_admin($user->get_user($_SESSION["username"])) == true){
    if($arbeit->add_worktime($data["start"], $data["end"], $data["location"], $data["date"], $data["username"], $data["type"], $data["pause"], $data["meta"])){
        echo json_encode(["note" => "Successfully saved worktime record"]);
    } else {
        echo json_encode(["error" => "An error occured while saving worktime"]);
    }
} else {
    echo json_encode(["error" => "No permission."]);
}