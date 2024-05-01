<?php
require dirname(__DIR__, 1) . "/api/v1/inc/arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Kalender;
use Arbeitszeit\Benutzer;
use Arbeitszeit\Auth;
@$username = $_SESSION["username"];
$auth = new Auth;
$calendar = new Kalender;
$user = new Benutzer;
$arbeit = new Arbeitszeit;
$base_url = Arbeitszeit::get_app_ini()["general"]["base_url"];
$ini = Arbeitszeit::get_app_ini();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Login | <?php echo $ini["general"]["app_name"]; ?></title>
        <link rel="stylesheet" type="text/css" href="/assets/css/index.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <h1>Bitte melde dich an!</h1>
        <form class="box" action="actions/auth/login.php" method="POST">
            <label>Nutzername: </label><input class="input" type="text" name="username" placeholder="MMustermann">
            <br><br>
            <label>Passwort: </label><input class="input" type="password" name="password" placeholder="Dein Passwort.">
            <br>
            <button class="button" type="submit" name="submit">Anmelden.</button>
            <br>
            <input type="checkbox" name="erinnern"><label title="Wenn das Häkchen gesetzt wird, wirst du für die nächsten 30 Tage automatisch angemeldet.">Für 30 Tage auf diesem Gerät erinnern.</label>
        </form>
        <br>
        <a href="forgot_password.php">Ich habe mein Passwort vergessen!</a>
    </body>
</html>