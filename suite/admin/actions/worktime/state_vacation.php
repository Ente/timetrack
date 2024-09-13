<?php
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/class/auth/plugins/mail_vacation_rejected.auth.arbeit.inc.php";
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/class/auth/plugins/mail_vacation_pending.auth.arbeit.inc.php";
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/class/auth/plugins/mail_vacation_approved.auth.arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\MailVacationApproved;
use Arbeitszeit\MailVacationPending;
use Arbeitszeit\MailVacationRejected;
use Arbeitszeit\Exceptions;
$avr = new MailVacationRejected;
$ava = new MailVacationApproved;
$avp = new MailVacationPending;
$arbeit = new Arbeitszeit;
$ini = $arbeit->get_app_ini();
$base_url = $ini["general"]["base_url"];
$id = $_GET["id"];
$vacation = $arbeit->vacation();
$arbeit->auth()->login_validation();
if ($arbeit->benutzer()->is_admin($arbeit->benutzer()->get_user($_SESSION["username"]))) {
    if (isset($_GET["id"], $_GET["new"])) {
        if (!$vacation->get_vacation($id, 2)) {
            Exceptions::error_rep("Could not find vacation with ID {$id}.");
            header("Location: http://{$base_url}/suite/?info=error");
            die();
        }
        switch ($_GET["new"]) {
            case "approve":
                if ($vacation->change_status($id, 1)) {
                    $ava->mail_vacation_approved($_GET["u"], $id, $auth->mail_init($_GET["u"], true));
                    header("Location: http://{$base_url}/suite/?info=changed_vacation");
                    die();
                } else {
                    Exceptions::error_rep("An error occured while changing status for vacation with id '{$id}'");
                    header("Location: http://{$base_url}/suite/?info=noperms");
                }
                break;
            case "pending":
                if ($vacation->change_status($id, 3)) {
                    $avp->mail_vacation_pending($_GET["u"], $id, $auth->mail_init($_GET["u"], true));
                    header("Location: http://{$base_url}/suite/?info=changed_vacation");
                    die();
                } else {
                    Exceptions::error_rep("An error occured while changing status for vacation with id '{$id}'");
                    header("Location: http://{$base_url}/suite/?info=noperms");
                }
                break;
            case "reject":
                if ($vacation->change_status($id, 2)) {
                    $avr->mail_vacation_rejected($_GET["u"], $id, $auth->mail_init($_GET["u"], true));
                    header("Location: http://{$base_url}/suite/?info=changed_vacation");
                    die();
                } else {
                    Exceptions::error_rep("An error occured while chaning status for vacation with id '{$id}'");
                    header("Location: http://{$base_url}/suite/?info=noperms");
                }
                break;
            default:
                if ($vacation->change_status($id, 3)) {
                    $ava->mail_vacation_approved($_GET["u"], $id, $auth->mail_init($_GET["u"], true));
                    header("Location: http://{$base_url}/suite/?info=changed_vacation");
                    die();
                } else {
                    Exceptions::error_rep("An error occured while chaning status for vacation with id '{$id}'");
                    header("Location: http://{$base_url}/suite/?info=noperms");
                }
                break;
        }
    }
} else {
    header("Location: http://{$base_url}/suite/?info=noperms");
}



?>