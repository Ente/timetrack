<?php
require "../../../../api/v1/inc/arbeit.inc.php";
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
if($user->is_admin($user->get_user($_SESSION["username"]))){
    if($calendar->edit_calendar_entry($_GET["id"], $_POST["uhrzeit"], $_POST["datum"], $_POST["ort"], $_POST["notiz"]) == true){
        header("Location: http://{$base_url}/suite/?info=calendar_entry_edited");
    }   
} else {
    header("Location: http://{$base_url}/suite/?info=noperms");
}


?>