<?php
use Arbeitszeit\Mailbox;
require dirname(__DIR__, 3) . "/api/v1/inc/arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Kalender;
use Arbeitszeit\Benutzer;
use Arbeitszeit\Auth;
$auth = new Auth;
$calendar = new Kalender;
$user = new Benutzer;
$arbeit = new Arbeitszeit;
$base_url = Arbeitszeit::get_app_ini()["general"]["base_url"];
$ini = Arbeitszeit::get_app_ini();
$mb = new Mailbox;

$auth->login_validation();
if(!$user->is_admin($user->get_user($_SESSION["username"]))){
    header("Location: http://{$base_url}/suite/?info=noperms");
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Mailbox-Eintrag hinzufügen | <?php echo $ini["general"]["app_name"]; ?></title>
        <link rel="stylesheet" type="text/css" href="../../../assets/css/index.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <?php include dirname(__DIR__, 3) . "/assets/gui/standard_nav.php"; ?>
        <h1>Mailbox-Eintrag hinzufügen | <?php echo $ini["general"]["app_name"]; ?></h1>
        <div class="box">
            <form action="../actions/mailbox/add.php" method="POST">
                <label>Name*: </label><br><input type="text" max="255" name="name" placeholder="Neue Benachrichtigung" required>
                    <hr>
                <label>Beschreibung**: </label><br><textarea name="description" placeholder="Beschreibung..."></textarea>
                    <br>
                <label>Benutzername*: </label><br><input type="text" name="user" placeholder="Benutzername" required>
                    <hr>
                <label>Datei-Link (Leer Lassen, falls keine Datei vorhanden): </label><br><input type="url" name="file" value="Link" placeholder="https://.....">
                    <br>
                <p>Falls du die Datei verschlüsseln möchtest, gebe hier ein Passwort ein: </p>
                <label>Passwort: </label><br><input type="password" name="file_password">
                <button type="submit">Submit!</button>
                <p style="font-size: small;">* = required, ** = If no description is given, "No description" will be displayed.</p>
            </form>
        </div>
    </body>
</html>