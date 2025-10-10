<?php
require dirname(__DIR__, 4) . "/api/v1/inc/arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
$arbeit = new Arbeitszeit;
$base_url = Arbeitszeit::get_app_ini()["general"]["base_url"];
$arbeit->auth()->login_validation();

if(!isset($_POST["name"], $_POST["items_assoc"], $_POST["owner"])){
    header("Location: http://{$base_url}/suite/?" . $arbeit->statusMessages()->URIBuilder("error"));
}

if($arbeit->benutzer()->is_admin($arbeit->benutzer()->get_user($_SESSION["username"]))){
    if($arbeit->projects()->addProject($_POST["name"], $_POST["description"], $_POST["items_assoc"], null, $_POST["owner"] ?? $arbeit->benutzer()->get_current_user()["id"])){
        header("Location: http://{$base_url}/suite/?" . $arbeit->statusMessages()->URIBuilder("project_added"));
    } else {
        header("Location: http://{$base_url}/suite/?" . $arbeit->statusMessages()->URIBuilder("project_added_error"));
    }
}