<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/class/auth/plugins/mail_password_reset.auth.arbeit.inc.php";
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Auth;
use Arbeitszeit\MailPasswordReset;
use Arbeitszeit\i18n;
use Arbeitszeit\DB;
$i18n = new i18n;
$arbeitszeit = new Arbeitszeit;
$auth = new Auth;
$ini = $arbeitszeit->get_app_ini();
$loc = $i18n->loadLanguage(NULL, "reset");
?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $loc["title"] ?> | <?php echo $ini["general"]["app_name"]; ?></title>
        <link rel="stylesheet" type="text/css" href="/assets/css/index.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>

        <form class="box" action="<?php $_SERVER["PHP_SELF"]; ?>" method="POST">
            <h2><?php echo $loc["title_q"] ?></h2>
            <p><?php echo $loc["request_mail"] ?></p>
            <label><?php echo $loc["label_email"] ?>: </label><input type="email" name="email" placeholder="you@mail.com">
            <br>
            <button class="button" type="submit" name="reset" value="true"><?php echo $loc["label_button"] ?></button>
        </form>
    </body>
</html>