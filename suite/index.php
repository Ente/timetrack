<?php
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
use Arbeitszeit\Arbeitszeit;
session_start();
$arbeit = new Arbeitszeit;
$arbeit->autodelete()->autodelete_obsolete_notifications_entries();
$ini = $arbeit->get_app_ini();
$base_url = $ini["general"]["base_url"];
$arbeit->auth()->login_validation();
$language = $arbeit->i18n()->loadLanguage(NULL, "index");
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
        <?php $arbeit->notifications()->get_notifications_html();  ?>
        <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/gui/standard_nav.php" ?> 
        <?php echo $arbeit->check_status_code($_SERVER["REQUEST_URI"]); ?>
        <h1><?php echo $language["title"] ?> | <?php echo $ini["general"]["app_name"]; ?></h1>
        <div class="box" style="padding:20px;">
            <h2><?php echo $language["action_addshift"] ?></h2>

            <?php echo $arbeit->mode()->check($_SESSION["username"]);   ?>

        </div>
            <p><?php echo $language["note_footer"] ?> <a href="mailto:<?php echo $ini["general"]["support_email"];  ?>"><?php echo $ini["general"]["support_email"];  ?></a></p>
    </body>
</html>