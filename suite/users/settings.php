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
$ini = Arbeitszeit::get_app_ini();
$auth->login_validation();


$data = $user->get_user($_SESSION["username"]);
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Einstellungen | <?php echo $ini["general"]["app_name"]; ?></title>
        <link rel="stylesheet" type="text/css" href="/assets/css/index.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <?php include dirname(__DIR__, 2) . "/assets/gui/standard_nav.php"; ?>
        <h2>Einstellungen</h2>
        <div class="box">
        <p>Dein Name: <?php echo $data["name"]; ?></p>
                    <p>Deine ID: <?php  echo $data["id"];    ?></p>
                    <p>Deine Email: <?php   echo $data["email"]    ?></p>
                    <p>Dein Nutzername: <?php echo $data["username"] ?></p>

                    <br>

                    <p>Wenn du diese Daten ändern möchtest, wende dich an deinen Chef.</p>
                    <p>Drücke <a href="http://<?php echo $ini["general"]["base_url"]; ?>/suite/users/changes.php">hier</a> um die aktuellsten Änderungen der Website zu sehen.</p>
                    <?php if($user->is_admin($user->get_user($_SESSION["username"]))){ echo "<p style='font-size:x-small;color:red'>YOU ARE USING AN ADMIN ACCOUNT!</p>"; }          ?>
                    <hr>
                    <?php  # add toggle for easymode and current status
                        echo "<p>Status - Easymode: " . $arbeit->get_easymode_status($_SESSION["username"]) . "</p>";
                    ?>
                    <br>
                    <form action="/suite/actions/users/toggle_easymode.php" method="POST">
                        <button class="button" type="submit">Easymode umschalten.</button>
                    </form>
                </div>

            <h2>Support</h2>
            <p>Solltest du Hilfe benötigen, kannst du folgende E-Mail kontaktieren: <a href="mailto:<?php echo $ini["general"]["support_email"]; ?>"><?php echo $ini["general"]["support_email"]; ?></a></p>
                <?php if($user->is_admin($user->get_user($_SESSION["username"]))){ require_once "../admin/users/settings.php"; }   ?>
        </div>
    </body>
</html>
