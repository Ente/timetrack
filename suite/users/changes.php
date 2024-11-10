<?php
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
$arbeit = new Arbeitszeit;
$base_url = $ini = Arbeitszeit::get_app_ini();
$arbeit->auth()->login_validation();
$locale = locale_accept_from_http($_SERVER["HTTP_ACCEPT_LANGUAGE"]); // Locale retrieved from Header
$loc = $arbeit->i18n()->loadLanguage($locale, "users/changes");
$data = $arbeit->benutzer()->get_user($_SESSION["username"]);
?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $loc["title"] ?> | <?php echo $ini["general"]["app_name"]; ?></title>
        <link rel="stylesheet" type="text/css" href="/assets/css/index.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="UTF-8">
    </head>
    <body>
        <?php $arbeit->notifications()->get_calendar_html()  ?>
        <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/gui/standard_nav.php" ?> 
        <?php echo $arbeit->check_status_code($_SERVER["REQUEST_URI"]); ?>
        <h1><?php echo $loc["title"] ?> | <?php echo $ini["general"]["app_name"]; ?></h1>
        <div class="box" style="padding:20px;">
            <h2><?php echo $loc["title"] ?></h2>
            <?php
            require $_SERVER["DOCUMENT_ROOT"] . "/vendor/autoload.php";
            $parsedown = new Parsedown();
            echo $parsedown->text(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/CHANGELOG.md"));

            ?>
        </div>
    </body>
</html>