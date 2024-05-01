<?php
require "../../../../api/v1/inc/arbeit.inc.php";
require "../../../../api/v1/class/auth/plugins/mail_worktime_deleted.auth.arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Kalender;
use Arbeitszeit\Benutzer;
use Arbeitszeit\Auth;
use Arbeitszeit\MailWorktimeDeleted;
$username = $_SESSION["username"];
$auth = new Auth;
$calendar = new Kalender;
$user = new Benutzer;
$num = new MailWorktimeDeleted;
$arbeit = new Arbeitszeit;
$base_url = $ini = Arbeitszeit::get_app_ini()["general"]["base_url"];
$id = $_GET["id"];
$auth->login_validation();
if($user->is_admin($user->get_user($_SESSION["username"]))){
    $num->mail_worktime_deleted($_GET["u"], $id, $auth->mail_init($_GET["u"], true));
    if($arbeit->delete_worktime($id) == true){
        header("Location: http://{$base_url}/suite/?info=worktime_deleted");
    }   
} else {
    header("Location http://{$base_url}/suite/?info=noperms");
}



?>