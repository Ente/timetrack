<?php
require dirname(__DIR__, 4) . "/api/v1/inc/arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Kalender;
use Arbeitszeit\Benutzer;
use Arbeitszeit\Auth;
$username = $_SESSION["username"];
$auth = new Auth;
$calendar = new Kalender;
$user = new Benutzer;
$arbeit = new Arbeitszeit;
$base_url = $ini = Arbeitszeit::get_app_ini()["general"]["base_url"];
$id = $_GET["id"];
$auth->login_validation();

if(!isset($_POST["datum"], $_POST["uhrzeit"], $_POST["ort"])){
    header("Location: http://{$base_url}/suite/?info=error");
}

if($user->is_admin($user->get_user($_SESSION["username"]))){
    if($calendar->create_calendar_entry($_POST["uhrzeit"], $_POST["datum"], $_POST["ort"], $_POST["notiz"]) == true){
        header("Location: http://{$base_url}/suite/?info=calendar_entry_added");
    }   
} else {
    header("Location: http://{$base_url}/suite/?info=noperms");
}


?>