<?php
require "../../../../api/v1/inc/arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Notifications;
use Arbeitszeit\Benutzer;
use Arbeitszeit\Auth;
use Arbeitszeit\Sickness;
use Arbeitszeit\Exceptions;
use Arbeitszeit\Mails\Provider\PHPMailerMailsProvider;

$username = $_SESSION["username"];
$auth = new Auth;
$notifications = new Notifications;
$user = new Benutzer;
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
        $arbeit->mails()->init(new PHPMailerMailsProvider($arbeit, $_SESSION["username"], true));
        switch ($_GET["new"]) {
            case "approve":
                if ($vacation->change_status($id, 1)) {
                    $arbeit->mails()->sendMail("SicknessApprovedTemplate", ["username" => $_GET["u"], "id" => $id]);
                    header("Location: http://{$base_url}/suite/?info=changed_sickness");
                    die();
                } else {
                    Exceptions::error_rep("An error occured while changing status for sickness with id '{$id}'");
                    header("Location: http://{$base_url}/suite/?info=noperms");
                }
                break;
            case "pending":
                if ($vacation->change_status($id, 3)) {
                    $arbeit->mails()->sendMail("SicknessPendingTemplate", ["username" => $_GET["u"], "id" => $id]);
                    header("Location: http://{$base_url}/suite/?info=changed_sickness");
                    die();
                } else {
                    Exceptions::error_rep("An error occured while changing status for sickness with id '{$id}'");
                    header("Location: http://{$base_url}/suite/?info=noperms");
                }
                break;
            case "reject":
                if ($vacation->change_status($id, 2)) {
                    $arbeit->mails()->sendMail("SicknessRejectedTemplate", ["username" => $_GET["u"], "id" => $id]);
                    header("Location: http://{$base_url}/suite/?info=changed_sickness");
                    die();
                } else {
                    Exceptions::error_rep("An error occured while changing status for sickness with id '{$id}'");
                    header("Location: http://{$base_url}/suite/?info=noperms");
                }
                break;
            default:
                if ($vacation->change_status($id, 3)) {
                    $arbeit->mails()->sendMail("SicknessApprovedTemplate", ["username" => $_GET["u"], "id" => $id]);
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