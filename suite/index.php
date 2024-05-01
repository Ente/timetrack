<?php
#ini_set("display_errors", 1);
require dirname(__DIR__, 1) . "/api/v1/inc/arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Kalender;
use Arbeitszeit\Benutzer;
use Arbeitszeit\Auth;
use Arbeitszeit\Autodelete;
use Arbeitszeit\Mode;
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

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Hauptmenü | <?php echo $ini["general"]["app_name"]; ?></title>
        <link rel="stylesheet" type="text/css" href="/assets/css/index.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="UTF-8">
    </head>
    <body>
        <?php $calendar->get_calendar_html()  ?>
        <?php include dirname(__DIR__, 1) . "/assets/gui/standard_nav.php" ?> 
        <?php echo $arbeit->check_status_code($_SERVER["REQUEST_URI"]); ?>
        <h1>Hauptmenü | <?php echo $ini["general"]["app_name"]; ?></h1>
        <div class="box" style="padding:20px;">
            <h2>Schicht eintragen</h2>
            <!--<form action="actions/worktime/add.php" method="POST">
                <label name="ort">Ort</label>
                <input type="text" name="ort" placeholder="Adresse, Bezeichnung, etc.">
                <br>
                <label name="date">Datum</label>
                <input type="date" name="date" data-date-format="DD.MM.YYYY">
                <br>
                <label name="schicht_beginn">Schicht Beginn</label>
                <input type="time" name="time_start" placeholder="Wann hat deine Schicht begonnen? (Uhrzeit)">
                <br>
                <label name="schicht_ende">Schicht Ende</label>
                <input type="time" name="time_end" placeholder="Wann war deine Schicht zuende? (UHrzeit)">
                <br>
                <input type="text" name="username" value="<?php echo $_SESSION["username"]; ?>" hidden>
                <button type="submit" class="button">Absenden!</button>
            </form>-->

            <?php echo Mode::check($username);   ?>

        </div>
            <p>Fragen? Schicke eine E-Mail an deinen Ansprechpartner: <a href="mailto:<?php echo $ini["general"]["support_email"];  ?>"><?php echo $ini["general"]["support_email"];  ?></a></p>
    </body>
</html>