<?php
#ini_set("display_errors", 1);
require "../../../../api/v1/inc/arbeit.inc.php";
require "../../../../api/v1/class/auth/plugins/mail_worktime_uncompliant.auth.arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Kalender;
use Arbeitszeit\Benutzer;
use Arbeitszeit\Auth;
use Arbeitszeit\MailWorktimeUncompliant;
$username = $_SESSION["username"];
$auth = new Auth;
$calendar = new Kalender;
$user = new Benutzer;
$num = new MailWorktimeUncompliant;
$arbeit = new Arbeitszeit;
$base_url = $ini = Arbeitszeit::get_app_ini()["general"]["base_url"];
$id = $_GET["id"];
$auth->login_validation();
if($user->is_admin($user->get_user($_SESSION["username"]))){
    if($arbeit->mark_for_review($id) == true){
        $num->mail_worktime_uncompliant($_GET["u"], $id, 1, $auth->mail_init($_GET["u"], true));
        header("Location: http://{$base_url}/suite/?info=worktime_review");
    } else {
        echo "Error while processing...";
    }
} else {
    header("Location http://{$base_url}/suite/?info=noperms");
}



?>