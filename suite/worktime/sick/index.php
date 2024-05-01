<?php
require dirname(__DIR__, 3) . "/api/v1/inc/arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Kalender;
use Arbeitszeit\Benutzer;
use Arbeitszeit\Auth;
use Arbeitszeit\Mailbox;
$username = $_SESSION["username"];
$auth = new Auth;
$calendar = new Kalender;
$user = new Benutzer;
$mb = new Mailbox;
$arbeit = new Arbeitszeit;
$base_url = $ini = Arbeitszeit::get_app_ini();

$auth->login_validation();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Krankheit eintragen | <?php echo $ini["general"]["app_name"]; ?></title>
        <link rel="stylesheet" type="text/css" href="../../../assets/css/index.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <?php include "../../../assets/gui/standard_nav.php"; ?>
        <h1>Krankheit eintragen | <?php echo $ini["general"]["app_name"]; ?></h1>
        <div class="box">
            <h2>Trage hier deine Krankheit ein</h2>
                <form action="/suite/actions/worktime/add_sick.php" method="POST">
                    <p>Bitte gib hier das Start-Datum deines Ausfalls an. Solltest du nur einen Tag ausfallen, dann fülle "End-Datum" bitte <span style="color:red;">nicht</span> aus.</p>
                    <input type="date" data-date-format="DD.MM.YYYY" name="date-start">
                    <br>
                    <p>End-Datum deiner Ausfalls*:</p>
                    <input type="date" data-date-format="DD.MM.YYYY" name="date-end">
                    <br>
                    <button type="submit" class="button">Krankheit einreichen.</button>
                </form>
                <br>
                <p>Nach dem Absenden wird dein Arbeitgeber die Krankheit überprüfen und sich bei dir, bei Bedarf, melden.</p>
        </div>
    </body>
</html>