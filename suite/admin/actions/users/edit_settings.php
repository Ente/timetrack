<?php
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
use Arbeitszeit\Arbeitszeit;
session_start();
$arbeit = new Arbeitszeit;
$ini = $arbeit->get_app_ini();
$base_url = $ini["general"]["base_url"];
$arbeit->auth()->login_validation();
if($arbeit->benutzer()->is_admin($arbeit->benutzer()->get_user($_SESSION["username"]))){
    # build array
    if(isset($_POST["app_name"])){
        $settings["app_name"] = $_POST["app_name"];
    }
    if(isset($_POST["base_url"])){
        $settings["base_url"] = $_POST["base_url"];
    }
    if(!isset($settings)){
        header("Location: http://{$base_url}/suite/?info=error");
    }
    if($arbeit->change_settings($settings) == true){
        header("Location: http://{$base_url}/suite/?info=settings_changed");
    }  
} else {
    header("Location: http://{$base_url}/suite/?info=noperms");
}
?>