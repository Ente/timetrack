<?php
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
$arbeit = new Arbeitszeit;
$ini = $arbeit->get_app_ini();
$base_url = $ini["general"]["base_url"];
$id = $_GET["id"];
$arbeit->auth()->login_validation();
if($arbeit->benutzer()->is_admin($arbeit->benutzer()->get_user($_SESSION["username"]))){
    if($arbeit->notifications()->delete_calendar_entry($id) == true){
        header("Location: http://{$base_url}/suite/?info=calendar_entry_deleted");
    }   
} else {
    header("Location: http://{$base_url}/suite/?info=noperms");
}


?>