<?php
require dirname(__DIR__, 2) . "/api/v1/inc/arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
$arbeit = new Arbeitszeit;
$ini = $arbeit->get_app_ini();
$arbeit->auth()->login_validation();

$locale = locale_accept_from_http($_SERVER["HTTP_ACCEPT_LANGUAGE"]); // Locale retrieved from Header
$loc = $arbeit->i18n()->loadLanguage(null, "users/settings");
$data = $arbeit->benutzer()->get_user($_SESSION["username"]);
?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $loc["title"] ?> | <?php echo $ini["general"]["app_name"]; ?></title>
        <link rel="stylesheet" type="text/css" href="/assets/css/index.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <?php include dirname(__DIR__, 2) . "/assets/gui/standard_nav.php"; ?>
        <h2><?php echo $loc["title"] ?></h2>
        <div class="box">
        <p><?php echo $loc["label_name"] ?>: <?php echo $data["name"]; ?></p>
                    <p><?php echo $loc["label_id"] ?>: <?php  echo $data["id"];    ?></p>
                    <p><?php echo $loc["label_email"] ?>: <?php   echo $data["email"]    ?></p>
                    <p><?php echo $loc["label_username"] ?>: <?php echo $data["username"] ?></p>
                    <p><?php echo $loc["label_provider"] ?>: <?php echo $_SESSION["provider"] ?></p>

                    <br>

                    <p><?php echo $loc["note1"] ?></p>
                    <p><?php echo $loc["note2_a"] ?> <a href="http://<?php echo $ini["general"]["base_url"]; ?>/suite/users/changes.php"><?php echo $loc["note2_b"] ?></a> <?php echo $loc["note2_c"] ?></p>
                    <?php if($arbeit->benutzer()->is_admin($arbeit->benutzer()->get_user($_SESSION["username"]))){ echo "<p style='font-size:x-small;color:red'>YOU ARE USING AN ADMIN ACCOUNT!</p>"; }          ?>
                    <hr>
                    <?php  # add toggle for easymode and current status
                        echo "<p>Status - Easymode: " . $arbeit->get_easymode_status($_SESSION["username"]) . "</p>";
                    ?>
                    <br>
                    <form action="/suite/actions/users/toggle_easymode.php" method="POST">
                        <button class="button" type="submit"><?php echo $loc["easymode_toggle"] ?></button>
                    </form>
                </div>

            <h2><?php echo $loc["support"] ?></h2>
            <p><?php echo $loc["support_note"] ?>: <a href="mailto:<?php echo $ini["general"]["support_email"]; ?>"><?php echo $ini["general"]["support_email"]; ?></a></p>
                <?php if($arbeit->benutzer()->is_admin($arbeit->benutzer()->get_user($_SESSION["username"]))){ require_once "../admin/users/settings.php"; }   ?>
        </div>
    </body>
</html>
