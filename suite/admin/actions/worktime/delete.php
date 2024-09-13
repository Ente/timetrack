<?php
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/class/auth/plugins/mail_worktime_deleted.auth.arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\MailWorktimeDeleted;
$num = new MailWorktimeDeleted;
$arbeit = new Arbeitszeit;
$ini = $arbeit->get_app_ini();
$base_url = $ini["general"]["base_url"];
$id = $_GET["id"];
$arbeit->auth()->login_validation();
if($arbeit->benutzer()->is_admin($arbeit->benutzer()->get_user($_SESSION["username"]))){
    $num->mail_worktime_deleted($_GET["u"], $id, $arbeit->auth()->mail_init($_GET["u"], true));
    if($arbeit->delete_worktime($id) == true){
        header("Location: http://{$base_url}/suite/?info=worktime_deleted");
    }   
} else {
    header("Location: http://{$base_url}/suite/?info=noperms");
}



?>