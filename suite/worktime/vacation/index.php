<?php
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Kalender;
use Arbeitszeit\Benutzer;
use Arbeitszeit\Auth;
use Arbeitszeit\i18n;
$username = $_SESSION["username"];
$auth = new Auth;
$calendar = new Kalender;
$user = new Benutzer;
$arbeit = new Arbeitszeit;
$base_url = $ini = Arbeitszeit::get_app_ini();
$i18n = new i18n;

$loc = $i18n->loadLanguage(null, "vacation");

$auth->login_validation();
?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $loc["title"] ?> | <?php echo $ini["general"]["app_name"]; ?></title>
        <link rel="stylesheet" type="text/css" href="/assets/css/index.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/gui/standard_nav.php"; ?>
        <h1><?php $loc["title"] ?> | <?php echo $ini["general"]["app_name"]; ?></h1>
        <div class="box">
            <h2><?php echo $loc["h2"] ?></h2>
                <form action="/suite/actions/worktime/add_vacation.php" method="POST">
                    <p><?php echo $loc["note1"] ?></p>
                    <input type="date" data-date-format="DD.MM.YYYY" name="date-start" required>
                    <br>
                    <p><?php echo $loc["note2"] ?>:</p>
                    <input type="date" data-date-format="DD.MM.YYYY" name="date-end">
                    <br>
                    <button type="submit" class="button"><?php echo $loc["button_submit"] ?></button>
                </form>
                <br>
                <p><?php echo $loc["note3"] ?></p>
        </div>
    </body>
</html>