<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
use Arbeitszeit\Arbeitszeit;
$arbeitszeit = new Arbeitszeit;
$ini = $arbeitszeit->get_app_ini();
$loc = $arbeitszeit->i18n()->loadLanguage(NULL, "reset");
?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $loc["title"] ?> | <?php echo $ini["general"]["app_name"]; ?></title>
        <link rel="stylesheet" type="text/css" href="/assets/css/index.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>

        <form class="box" action="actions/auth/reset.php" method="POST">
            <h2><?php echo $loc["title_q"] ?></h2>
            <p><?php echo $loc["request_mail"] ?></p>
            <label><?php echo $loc["label_email"] ?>: </label><input class="input" type="email" name="email" placeholder="you@mail.com">
            <br>
            <button class="button" type="submit" name="reset" value="true"><?php echo $loc["label_button"] ?></button>
        </form>
    </body>
</html>