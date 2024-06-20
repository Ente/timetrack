<?php
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
use Arbeitszeit\Exceptions;
if($ini["general"]["app"] == "true"){
    ini_set("session.cookie_samesite", "None");
    header('P3P: CP="CAO PSA OUR"');
    Exceptions::error_rep("Enabling samesite for user '$username'!");
    session_set_cookie_params(["path" => "/", "domain" => $ini["general"]["base_url"], "secure" => true, "samesite" => "None"]);
    session_regenerate_id(true);
}
session_start();
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Kalender;
use Arbeitszeit\Benutzer;
use Arbeitszeit\Auth;
use Arbeitszeit\Autodelete;
use Arbeitszeit\Mode;
use Arbeitszeit\i18n;
$i18n = new i18n;
$username = $_SESSION["username"];
$auth = new Auth;
$calendar = new Kalender;
$ad = new Autodelete;
$ad->autodelete_obsolete_calendar_entries();
$user = new Benutzer;
$arbeit = new Arbeitszeit;
$base_url = Arbeitszeit::get_app_ini()["general"]["base_url"];
$ini = Arbeitszeit::get_app_ini();
$auth->login_validation();

$language = $i18n->loadLanguage(NULL, "index");

?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $language["title"] ?> | <?php echo $ini["general"]["app_name"]; ?></title>
        <link rel="stylesheet" type="text/css" href="/assets/css/index.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="UTF-8">
    </head>
    <body>
        <?php $calendar->get_calendar_html()  ?>
        <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/gui/standard_nav.php" ?> 
        <?php echo $arbeit->check_status_code($_SERVER["REQUEST_URI"]); ?>
        <h1><?php echo $language["title"] ?> | <?php echo $ini["general"]["app_name"]; ?></h1>
        <div class="box" style="padding:20px;">
            <h2><?php echo $language["action_addshift"] ?></h2>

            <?php echo Mode::check($username);   ?>

        </div>
            <p><?php echo $language["note_footer"] ?> <a href="mailto:<?php echo $ini["general"]["support_email"];  ?>"><?php echo $ini["general"]["support_email"];  ?></a></p>
    </body>
</html>