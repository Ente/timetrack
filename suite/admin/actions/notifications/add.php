<?php
require dirname(__DIR__, 4) . "/api/v1/inc/arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
$arbeit = new Arbeitszeit;
$ini = $arbeit->get_app_ini();
$base_url = $ini["general"]["base_url"];
$arbeit->auth()->login_validation();

if(!isset($_POST["datum"], $_POST["uhrzeit"], $_POST["ort"])){
    header("Location: http://{$base_url}/suite/?" . $arbeit->statusMessages()->URIBuilder("error"));
}

if($arbeit->benutzer()->is_admin($arbeit->benutzer()->get_user($_SESSION["username"]))){
    if($arbeit->notifications()->create_notifications_entry($_POST["uhrzeit"], $_POST["datum"], $_POST["ort"], $_POST["notiz"]) == true){
        header("Location: http://{$base_url}/suite/?" . $arbeit->statusMessages()->URIBuilder("notifications_entry_added"));
    }   
} else {
    header("Location: http://{$base_url}/suite/?" . $arbeit->statusMessages()->URIBuilder("noperms"));
}


?>