<?php

require "../../../../api/v1/inc/arbeit.inc.php";
require "../../../../api/v1/class/auth/plugins/mail_delete_user.auth.arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Kalender;
use Arbeitszeit\Benutzer;
use Arbeitszeit\Auth;
use Arbeitszeit\MailDeleteUser;
$username = $_SESSION["username"];
$auth = new Auth;
$calendar = new Kalender;
$user = new Benutzer;
$arbeit = new Arbeitszeit;
$mps = new MailDeleteUser;
$id = $_GET["id"];
$base_url = $ini = Arbeitszeit::get_app_ini()["general"]["base_url"];
$auth->login_validation();
if($user->is_admin($user->get_user($_SESSION["username"]))){
    if($user->get_user_from_id($id)["username"] == "api"){
        header("Location: http://{$base_url}/suite/?info=error");
        die();
    }
    $mps->mail_delete_user($user->get_user_from_id($id)["username"], $auth->mail_init($user->get_user_from_id($id)["username"], true));
    if($user->delete_user($id) == true){
        header("Location: http://{$base_url}/suite/?info=user_deleted");
    }  
} else {
    header("Location http://{$base_url}/suite/?info=noperms");
}
?>