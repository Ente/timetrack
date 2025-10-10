<?php
require dirname(__DIR__, 4) . "/api/v1/inc/arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
$arbeit = new Arbeitszeit;
$base_url = $arbeit->get_app_ini()["general"]["base_url"];
$arbeit->auth()->login_validation();

if(!isset($_GET["id"])){
    header("Location: http://{$base_url}/suite/?" . $arbeit->statusMessages()->URIBuilder("error"));
}

if($arbeit->benutzer()->is_admin($arbeit->benutzer()->get_user($_SESSION["username"])) && $arbeit->projects()->checkUserisOwner($_GET["id"])){
    if($arbeit->projects()->deleteProject($_GET["id"])){
        header("Location: http://{$base_url}/suite/?" . $arbeit->statusMessages()->URIBuilder("project_deleted"));
    } else {
        header("Location: http://{$base_url}/suite/?" . $arbeit->statusMessages()->URIBuilder("project_deleted_failed"));
    }
}