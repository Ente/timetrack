<?php
require $_SERVER['DOCUMENT_ROOT'] . "/api/v1/inc/arbeit.inc.php";
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/class/auth/plugins/mail_worktime_uncompliant.auth.arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\MailWorktimeUncompliant;
$num = new MailWorktimeUncompliant;
$arbeit = new Arbeitszeit;
$ini = $arbeit->get_app_ini();
$base_url = $ini["general"]["base_url"];
$id = $_GET["id"];
$arbeit->auth()->login_validation();
if($arbeit->benutzer()->is_admin($arbeit->benutzer()->get_user($_SESSION["username"]))){
    if($arbeit->unlock_for_review($id) == true){
        $num->mail_worktime_uncompliant($_GET["u"], $id, 0, $auth->mail_init($_GET["u"], true));
        header("Location: http://{$base_url}/suite/?info=worktime_review_unlock");
    } else {
        echo "Error while processing...";
    }
} else {
    header("Location: http://{$base_url}/suite/?info=noperms");
}



?>