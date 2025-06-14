<?php
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/class/plugins/loader.plugins.arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
$arbeit = new Arbeitszeit;
use Arbeitszeit\PluginBuilder;
use NFClogin\NFClogin;
$ini = $arbeit->get_app_ini();
$base_url = $ini["general"]["base_url"];
$language = $arbeit->i18n()->loadLanguage(NULL, "login");
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
        <?php
        $pl = new PluginBuilder();
        if($pl->read_plugin_configuration("nfclogin")["enabled"] == "true"){
            require_once dirname(__DIR__, 1) . "/api/v1/class/plugins/plugins/nfclogin/src/Main.php";
            $nfc = new NFClogin;
            echo "<br>" . $nfc->nfcloginHtml() . "<br>";
        }


        ?>
        <a href="forgot_password.php"><?php echo $language["forgot_pw"] ?></a>
    </body>
</html>