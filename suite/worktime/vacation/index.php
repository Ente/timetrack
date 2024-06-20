<?php
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
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
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Urlaub eintragen | <?php echo $ini["general"]["app_name"]; ?></title>
        <link rel="stylesheet" type="text/css" href="/assets/css/index.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/gui/standard_nav.php"; ?>
        <h1>Urlaub eintragen | <?php echo $ini["general"]["app_name"]; ?></h1>
        <div class="box">
            <h2>Trage hier deinen Urlaub ein</h2>
                <form action="/suite/actions/worktime/add_vacation.php" method="POST">
                    <p>Bitte gib hier das Start-Datum deines Urlaubs an. Solltest du nur einen Tag Urlaub nehmen wollen, dann fülle "End-Datum" bitte <span style="color:red;">nicht</span> aus.</p>
                    <input type="date" data-date-format="DD.MM.YYYY" name="date-start">
                    <br>
                    <p>End-Datum deines Urlaubs*:</p>
                    <input type="date" data-date-format="DD.MM.YYYY" name="date-end">
                    <br>
                    <button type="submit" class="button">Urlaub einreichen.</button>
                </form>
                <br>
                <p>Nach dem Absenden wird dein Arbeitgeber deinen Urlaub überprüfen und sich bei dir, bei Bedarf, melden.</p>
        </div>
    </body>
</html>