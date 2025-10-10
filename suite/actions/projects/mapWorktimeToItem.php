<?php
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
$arbeit = new Arbeitszeit;
$base_url = $arbeit->get_app_ini()["general"]["base_url"];
$arbeit->auth()->login_validation();

if(!isset($_POST["worktime_id"], $_POST["item_id"])){
    header("Location: http://{$base_url}/suite/?" . $arbeit->statusMessages()->URIBuilder("error"));
}

if($_POST["user_id"] == null){
    $user_id = $arbeit->benutzer()->get_current_user()["id"];
}

if(($arbeit->projects()->checkUserisOwner($_POST["project_id"]) || $arbeit->projects()->checkUserHasProjectAccess($arbeit->benutzer()->get_current_user()["id"], $_POST["project_id"], 0))){
    if($arbeit->projects()->mapWorktimeToItem($_POST["worktime_id"], $_POST["item_id"])){
        $arbeit->statusMessages()->redirect("mapWorktimeToItem_success");
    } else {
        $arbeit->statusMessages()->redirect("mapWorktimeToItem_failed");
    }
}