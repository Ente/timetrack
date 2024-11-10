<?php
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
$arbeit = new Arbeitszeit;
$ini = $arbeit->get_app_ini();
$base_url = $ini["general"]["base_url"];
$arbeit->auth()->login_validation();
if($arbeit->benutzer()->is_admin($arbeit->benutzer()->get_user($_SESSION["username"]))){
    if($arbeit->notifications()->edit_calendar_entry($_GET["id"], $_POST["uhrzeit"], $_POST["datum"], $_POST["ort"], $_POST["notiz"]) == true){
        header("Location: http://{$base_url}/suite/?info=calendar_entry_edited");
    }   
} else {
    header("Location: http://{$base_url}/suite/?info=noperms");
}


?>