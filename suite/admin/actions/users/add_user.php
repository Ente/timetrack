<?php

use Arbeitszeit\Mails\Provider\PHPMailerMailsProvider;
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";

session_start();
use Arbeitszeit\Arbeitszeit;
$arbeit = new Arbeitszeit;
$ini = $arbeit->get_app_ini();
$base_url = $ini["general"]["base_url"];
$arbeit->auth()->login_validation();
if($arbeit->benutzer()->is_admin($arbeit->benutzer()->get_user($_SESSION["username"]))){
        $isAdmin = ($_POST["admin"] == true) ? 1 : 0;
        if($arbeit->benutzer()->create_user($_POST["username"], $_POST["name"], $_POST["email"], $_POST["password"], $isAdmin) == true){
            $provider = new PHPMailerMailsProvider($arbeit, $arbeit->benutzer()->get_user($_SESSION["username"]), true);
            $arbeit->mails()->init($provider);
            $arbeit->mails()->sendMail("NewUserTemplate", ["username" => $_POST["username"]]);
            $arbeit->mails()->sendMail("PasswordSendTemplate", ["username" => $_POST["username"], "password" => $_POST["password"]]);
            echo "<meta http-equiv='refresh' content='0; url=http://{$base_url}/suite/? " . $arbeit->statusMessages()->URIBuilder("user_added") . "'>";
        } else {
            echo "<meta http-equiv='refresh' content='0; url=http://{$base_url}/suite/?" . $arbeit->statusMessages()->URIBuilder("error") . "'>";
        } 
} else {
    header("Location: http://{$base_url}/suite/?" . $arbeit->statusMessages()->URIBuilder("noperms"));
}
?>