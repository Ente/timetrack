<?php
require dirname(__DIR__, 2) . "/api/v1/inc/arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Kalender;
use Arbeitszeit\Benutzer;
use Arbeitszeit\Auth;
$username = $_SESSION["username"];
$auth = new Auth;
$calendar = new Kalender;
$user = new Benutzer;
$arbeit = new Arbeitszeit;
$base_url = $ini = Arbeitszeit::get_app_ini();
$auth->login_validation();


$data = $user->get_user($_SESSION["username"]);


?>
<!DOCTYPE html>
<html>
    <head>
        <title>Änderungen | <?php echo $ini["general"]["app_name"]; ?></title>
        <link rel="stylesheet" type="text/css" href="/assets/css/index.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="UTF-8">
    </head>
    <body>
        <?php $calendar->get_calendar_html()  ?>
        <?php include dirname(__DIR__, 2) . "/assets/gui/standard_nav.php" ?> 
        <?php echo $arbeit->check_status_code($_SERVER["REQUEST_URI"]); ?>
        <h1>Hauptmenü | <?php echo $ini["general"]["app_name"]; ?></h1>
        <div class="box" style="padding:20px;">
            <h2>Änderungen</h2>
            <?php
            require dirname(__DIR__, 2) . "/vendor/autoload.php";
            $parsedown = new Parsedown();
            echo $parsedown->text(file_get_contents(dirname(__DIR__, 2) . "/CHANGELOG.md"));

            ?>
        </div>
    </body>
</html>