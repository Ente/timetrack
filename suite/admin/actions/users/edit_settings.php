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
$auth->login_validation();
if($user->is_admin($user->get_user($_SESSION["username"]))){

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
    header("Location http://{$base_url}/suite/?info=noperms");
}
?>