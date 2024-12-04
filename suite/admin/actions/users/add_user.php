<?php
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/class/auth/plugins/mail_new_user.auth.arbeit.inc.php";
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/class/auth/plugins/mail_password_send.auth.arbeit.inc.php";

session_start();
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\MailPasswordNewUser;
use Arbeitszeit\MailPasswordSend;
$arbeit = new Arbeitszeit;
$num = new MailPasswordNewUser;
$mps = new MailPasswordSend;
$ini = $arbeit->get_app_ini();
$base_url = $ini["general"]["base_url"];
$arbeit->auth()->login_validation();
if($arbeit->benutzer()->is_admin($arbeit->benutzer()->get_user($_SESSION["username"]))){
        $isAdmin = ($_POST["admin"] == true) ? 1 : 0;
        if($arbeit->benutzer()->create_user($_POST["username"], $_POST["name"], $_POST["email"], $_POST["password"], $isAdmin) == true){
            $num->mail_password_new_user($_POST["username"] ,$arbeit->auth()->mail_init($_POST["username"], true));
            $mps->mail_password_send($_POST["username"], 2, $arbeit->auth()->mail_init($_POST["username"], true), $_POST["password"]);
            echo "<meta http-equiv='refresh' content='0; url=http://{$base_url}/suite/?info=created_user'>";
        } else {
            echo "<meta http-equiv='refresh' content='0; url=http://{$base_url}/suite/?info=error'>";
        } 
} else {
    header("Location: http://{$base_url}/suite/?info=noperms");
}
?>