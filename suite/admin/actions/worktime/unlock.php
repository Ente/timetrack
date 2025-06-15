<?php

use Arbeitszeit\Mails\Provider\PHPMailerMailsProvider;
require $_SERVER['DOCUMENT_ROOT'] . "/api/v1/inc/arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
$arbeit = new Arbeitszeit;
$ini = $arbeit->get_app_ini();
$base_url = $ini["general"]["base_url"];
$id = $_GET["id"];
$arbeit->auth()->login_validation();
$auth = $arbeit->auth();
if($arbeit->benutzer()->is_admin($arbeit->benutzer()->get_user($_SESSION["username"]))){
    if($arbeit->unlock_for_review($id) == true){
        $arbeit->mails()->init(new PHPMailerMailsProvider($arbeit, $_SESSION["username"], true));
        $arbeit->mails()->sendMail("WorktimeUncompliantTemplate", ["username" => $_GET["u"], "worktime" => $id, "type" => 0]);
        header("Location: http://{$base_url}/suite/?" . $arbeit->statusMessages()->URIBuilder("worktime_review_unlocked"));
    } else {
        echo "Error while processing...";
    }
} else {
    header("Location: http://{$base_url}/suite/?" . $arbeit->statusMessages()->URIBuilder("noperms"));
}



?>