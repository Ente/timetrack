<?php
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Kalender;
use Arbeitszeit\Benutzer;
use Arbeitszeit\Auth;
use Arbeitszeit\i18n;
$i18n = new i18n;
@$username = $_SESSION["username"];
$auth = new Auth;
$calendar = new Kalender;
$user = new Benutzer;
$arbeit = new Arbeitszeit;
$base_url = Arbeitszeit::get_app_ini()["general"]["base_url"];
$ini = Arbeitszeit::get_app_ini();

$language = $i18n->loadLanguage(NULL, "login");
?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $language["title"] ?> | <?php echo $ini["general"]["app_name"]; ?></title>
        <link rel="stylesheet" type="text/css" href="/assets/css/index.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <h1><?php echo $language["h1"] ?></h1>
        <form class="box" action="actions/auth/login.php" method="POST">
            <label><?php echo $language["label_username"] ?>: </label><input class="input" type="text" name="username" placeholder="<?php echo $language["placeholder_username"] ?>">
            <br><br>
            <label><?php echo $language["label_password"] ?>: </label><input class="input" type="password" name="password" placeholder="<?php echo $language["placeholder_password"] ?>">
            <br>
            <button class="button" type="submit" name="submit"><?php echo $language["button_text"] ?></button>
            <br>
            <input type="checkbox" name="erinnern"><label title="Wenn das Häkchen gesetzt wird, wirst du für die nächsten 30 Tage automatisch angemeldet."><?php echo $language["checkbox_30days"] ?></label>
        </form>
        <br>
        <a href="forgot_password.php"><?php echo $language["forgot_pw"] ?></a>
    </body>
</html>