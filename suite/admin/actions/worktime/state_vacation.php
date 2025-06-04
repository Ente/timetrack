<?php
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Exceptions;
use Arbeitszeit\Mails\Provider\PHPMailerMailsProvider;
$arbeit = new Arbeitszeit;
$ini = $arbeit->get_app_ini();
$base_url = $ini["general"]["base_url"];
$id = $_GET["id"];
$vacation = $arbeit->vacation();
$arbeit->auth()->login_validation();
$auth = $arbeit->auth();
if ($arbeit->benutzer()->is_admin($arbeit->benutzer()->get_user($_SESSION["username"]))) {
    if (isset($_GET["id"], $_GET["new"])) {
        if (!$vacation->get_vacation($id, 2)) {
            Exceptions::error_rep("Could not find vacation with ID {$id}.");
            header("Location: http://{$base_url}/suite/?info=error");
            die();
        }
        $arbeit->mails()->init(new PHPMailerMailsProvider($arbeit, $_SESSION["username"], true));
        switch ($_GET["new"]) {
            case "approve":
                if ($vacation->change_status($id, 1)) {
                    $arbeit->mails()->sendMail("VacationApprovedTemplate", ["username" => $_GET["u"], "id" => $id]);
                    header("Location: http://{$base_url}/suite/?info=changed_vacation");
                    die();
                } else {
                    Exceptions::error_rep("An error occurred while changing status for vacation with id '{$id}'");
                    header("Location: http://{$base_url}/suite/?info=noperms");
                }
                break;
            case "pending":
                if ($vacation->change_status($id, 3)) {
                    $arbeit->mails()->sendMail("VacationPendingTemplate", ["username" => $_GET["u"], "id" => $id]);
                    header("Location: http://{$base_url}/suite/?info=changed_vacation");
                    die();
                } else {
                    Exceptions::error_rep("An error occurred while changing status for vacation with id '{$id}'");
                    header("Location: http://{$base_url}/suite/?info=noperms");
                }
                break;
            case "reject":
                if ($vacation->change_status($id, 2)) {
                    $arbeit->mails()->sendMail("VacationRejectedTemplate", ["username" => $_GET["u"], "id" => $id]);
                    header("Location: http://{$base_url}/suite/?info=changed_vacation");
                    die();
                } else {
                    Exceptions::error_rep("An error occurred while changing status for vacation with id '{$id}'");
                    header("Location: http://{$base_url}/suite/?info=noperms");
                }
                break;
            default:
                if ($vacation->change_status($id, 3)) {
                    $arbeit->mails()->sendMail("VacationApprovedTemplate", ["username" => $_GET["u"], "id" => $id]);
                    header("Location: http://{$base_url}/suite/?info=changed_vacation");
                    die();
                } else {
                    Exceptions::error_rep("An error occurred while changing status for vacation with id '{$id}'");
                    header("Location: http://{$base_url}/suite/?info=noperms");
                }
                break;
        }
    }
} else {
    header("Location: http://{$base_url}/suite/?info=noperms");
}



?>