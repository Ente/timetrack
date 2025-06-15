<?php
session_start();
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
use Arbeitszeit\Arbeitszeit;
$arbeit = new Arbeitszeit;
$ini = $arbeit->get_app_ini();
$base_url = $ini["general"]["base_url"];

$language = $arbeit->i18n()->loadLanguage(null, "users/edit", "admin");

$arbeit->auth()->login_validation();
if(!$arbeit->benutzer()->is_admin($arbeit->benutzer()->get_user($_SESSION["username"]))){
    header("Location: http://{$base_url}/suite/?" . $arbeit->statusMessages()->URIBuilder("noperms"));
}
$add_style = "";


?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $language["title"] ?> | <?php echo $ini["general"]["app_name"]; ?></title>
        <link rel="stylesheet" type="text/css" href="/assets/css/index.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <?php  include $_SERVER["DOCUMENT_ROOT"] . "/assets/gui/standard_nav.php" ?>

        <h1><?php echo $language["title"] ?></h1>
        <div class="box">
            <p><?php echo $language["p1"] ?></p>
            <h2><?php echo $language["title"] ?></h2>
                <table style="width:100%;">
                    <tr>
                        <th><?php echo $language["th1"] ?></th>
                        <th><?php echo $language["th2"] ?></th>
                        <th><?php echo $language["th3"] ?></th>
                        <th><?php echo $language["th4"] ?></th>
                    </tr>
                    <?php $arbeit->benutzer()->get_all_users_html(); ?>
                </table>

                <h2><?php echo $language["add_user"] ?></h2>
                <form action="../../admin/actions/users/add_user.php" method="POST">
                    <label title="Der Username besteht aus Anfangsbuchstabe des Vornamens großgeschrieben und voller Nachname, mit großem Anfangsbuchstaben. Beispiel: MMueller">Username: </label><input class="input" type="text" name="username" placeholder="MMustermann">
                    <br>
                    <label><?php echo $language["label_firstname"] ?>:</label><input class="input" type="text" name="name" placeholder="Name">
                    <br>
                    <label><?php echo $language["label_email"] ?>:</label><input class="input" type="email" name="email" required placeholder="Email">
                    <br>
                    <label><?php echo $language["label_password"] ?>:</label><input class="input" type="password" name="password" placeholder="Your Password">
                    <br>
                    <label><?php echo $language["label_grant_admin"] ?></label><input type="checkbox" value="true" name="admin">
                    <button class="button" type="submit" name="submit" class="button" <?php echo $add_style;  ?>><?php echo $language["button_text"] ?></button>
                </form>
        </div>
    </body>
</html>