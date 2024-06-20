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
$base_url = Arbeitszeit::get_app_ini()["general"]["base_url"];
$ini = Arbeitszeit::get_app_ini();
$auth->login_validation();
if(!@$user->is_admin($_SESSION["username"])){
    header("Location http://{$base_url}/suite/?info=noperms");
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Alle Arbeitszeiten | <?php echo $ini["general"]["app_name"]; ?></title>
        <link rel="stylesheet" type="text/css" href="/assets/css/index.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <?php require $_SERVER["DOCUMENT_ROOT"] . "/assets/gui/standard_nav.php"; ?>
        <h1>Kalendar</h1>
        <div class="box">
            <h2>Bitte trage etwas ein!</h2>
            <p>Das von dir Eingegebene wird auf der Startseite und hier im Kalendar angezeigt.</p>
            <p>Um einen Eintrag zu löschen oder als erledigt zu markieren, drücke bitte auf "Löschen".</p>
            <p><span style="color:red;">Zu viele Einträge machen die Startseite unübersichtlich!</span></p>
        </div>

        <div class="box">
            <form method="POST" action="../admin/actions/calendar/add.php">
                <label>Datum: </label><input type="date" name="datum" required>
                <br>
                <label>Uhrzeit: </label><input type="time" name="uhrzeit" required>
                <br>
                <label>Ort: </label><input type="text" name="ort" placeholder="Ort">
                <br>
                <label>Notiz: </label><textarea type="text" name="notiz" placeholder="Trage hier eine Notiz ein."></textarea>
                <br>
                <button type="submit">Absenden</button>
            </form>
        </div>

        <div class="box">
            <h2>Alle Einträge: </h2>
            <table style="width:100%">
                <tr>
                    <th>- - - -</th>
                    <th>Datum</th>
                    <th>Uhrzeit</th>
                    <th>Ort</th>
                    <th>Notiz</th>
                </tr>

                <?php echo $calendar->get_calendar_edit_html(); ?>
            </table>
        </div>
    </body>
</html>