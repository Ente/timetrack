<?php
require dirname(__DIR__, 4) . "/api/v1/inc/arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
$arbeit = new Arbeitszeit;
$base_url = $arbeit->get_app_ini()["general"]["base_url"];

$arbeit->auth()->login_validation();

if(!isset($_POST["name"], $_POST["owner"]) || !isset($_POST["id"])){
    header("Location: http://{$base_url}/suite/?" . $arbeit->statusMessages()->URIBuilder("error"));
}

if($arbeit->benutzer()->is_admin($arbeit->benutzer()->get_current_user()) && ($arbeit->benutzer()->current_user_is_admin() || $arbeit->projects()->checkUserisOwner($_POST["id"]))){
    if($arbeit->projects()->editProject($_POST["id"], [
        "name" => $_POST["name"],
        "description" => $_POST["description"],
        "deadline" => $_POST["deadline"],
        "owner" => $_POST["owner"]
    ])){
        header("Location: http://{$base_url}/suite/?" . $arbeit->statusMessages()->URIBuilder("project_edited"));
    } else {
        header("Location: http://{$base_url}/suite/?" . $arbeit->statusMessages()->URIBuilder("project_edited_error"));
    }
}