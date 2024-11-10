<?php
require "../../../../api/v1/inc/arbeit.inc.php";
require "../../../../api/v1/class/auth/plugins/mail_sickness_rejected.auth.arbeit.inc.php";
require "../../../../api/v1/class/auth/plugins/mail_sickness_pending.auth.arbeit.inc.php";
require "../../../../api/v1/class/auth/plugins/mail_sickness_approved.auth.arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Notifications;
use Arbeitszeit\Benutzer;
use Arbeitszeit\Auth;
use Arbeitszeit\MailSicknessApproved;
use Arbeitszeit\MailSicknessPending;
use Arbeitszeit\MailSicknessRejected;
use Arbeitszeit\Sickness;
use Arbeitszeit\Exceptions;

$username = $_SESSION["username"];
$auth = new Auth;
$calendar = new Notifications;
$user = new Benutzer;
$avr = new MailSicknessRejected;
$ava = new MailSicknessApproved;
$avp = new MailSicknessPending;
$arbeit = new Arbeitszeit;
$base_url = $ini = Arbeitszeit::get_app_ini()["general"]["base_url"];
$id = $_GET["id"];
$vacation = new Sickness;
$auth->login_validation();
if ($user->is_admin($user->get_user($_SESSION["username"]))) {
    if (isset($_GET["id"], $_GET["new"])) {
        if (!$vacation->get_sickness($id, 2)) {
            Exceptions::error_rep("Could not find vacation with ID {$id}.");
            header("Location: http://{$base_url}/suite/?info=error");
            die();
        }
        switch ($_GET["new"]) {
            case "approve":
                if ($vacation->change_status($id, 1)) {
                    $ava->mail_sickness_approved($_GET["u"], $id, $auth->mail_init($_GET["u"], true));
                    header("Location: http://{$base_url}/suite/?info=changed_sickness");
                    die();
                } else {
                    Exceptions::error_rep("An error occured while changing status for sickness with id '{$id}'");
                    header("Location: http://{$base_url}/suite/?info=noperms");
                }
                break;
            case "pending":
                if ($vacation->change_status($id, 3)) {
                    $avp->mail_sickness_pending($_GET["u"], $id, $auth->mail_init($_GET["u"], true));
                    header("Location: http://{$base_url}/suite/?info=changed_sickness");
                    die();
                } else {
                    Exceptions::error_rep("An error occured while changing status for sickness with id '{$id}'");
                    header("Location: http://{$base_url}/suite/?info=noperms");
                }
                break;
            case "reject":
                if ($vacation->change_status($id, 2)) {
                    $avr->mail_sickness_rejected($_GET["u"], $id, $auth->mail_init($_GET["u"], true));
                    header("Location: http://{$base_url}/suite/?info=changed_sickness");
                    die();
                } else {
                    Exceptions::error_rep("An error occured while changing status for sickness with id '{$id}'");
                    header("Location: http://{$base_url}/suite/?info=noperms");
                }
                break;
            default:
                if ($vacation->change_status($id, 3)) {
                    $ava->mail_sickness_approved($_GET["u"], $id, $auth->mail_init($_GET["u"], true));
                    header("Location: http://{$base_url}/suite/?info=changed_sickness");
                    die();
                } else {
                    Exceptions::error_rep("An error occured while chaning status for sickness with id '{$id}'");
                    header("Location: http://{$base_url}/suite/?info=noperms");
                }
                break;
        }
    }
} else {
    header("Location: http://{$base_url}/suite/?info=noperms");
}



?>