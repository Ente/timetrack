<?php
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
$arbeit = new Arbeitszeit;
$ini = $arbeit->get_app_ini();
$base_url = $ini["general"]["base_url"];
$loc = $arbeit->i18n()->loadLanguage(null, "notifications/all");
$arbeit->auth()->login_validation();
if(!$arbeit->benutzer()->is_admin($arbeit->benutzer()->get_user($_SESSION["username"]))){
    header("Location: http://{$base_url}/suite/?info=noperms");
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $loc["title"] ?> | <?php echo $ini["general"]["app_name"]; ?></title>
        <link rel="stylesheet" type="text/css" href="/assets/css/index.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <?php require $_SERVER["DOCUMENT_ROOT"] . "/assets/gui/standard_nav.php"; ?>
        <h1><?php echo $loc["title"] ?></h1>
        <div class="box">
            <h2><?php echo $loc["note1"] ?></h2>
            <p><?php echo $loc["p1"] ?></p>
            <p><?php echo $loc["p2"] ?></p>
            <p><span style="color:red;"><?php echo $loc["note2"] ?></span></p>
        </div>

        <div class="box">
            <form method="POST" action="../admin/actions/notifications/add.php">
                <label><?php echo $loc["label_date"] ?>: </label><input type="date" name="datum" required>
                <br>
                <label><?php echo $loc["label_time"] ?>: </label><input type="time" name="uhrzeit" required>
                <br>
                <label><?php echo $loc["label_location"] ?>: </label><input type="text" name="ort" placeholder="Ort">
                <br>
                <label><?php echo $loc["label_note"] ?>: </label><textarea type="text" name="notiz" placeholder="<?php echo $loc["pl_note"] ?>"></textarea>
                <br>
                <button type="submit"><?php echo $loc["button_send"] ?></button>
            </form>
        </div>

        <div class="box">
            <h2><?php echo $loc["a_entries"] ?>: </h2>
            <table style="width:100%">
                <tr>
                    <th>- - - -</th>
                    <th><?php echo $loc["label_date"] ?></th>
                    <th><?php echo $loc["label_time"] ?></th>
                    <th><?php echo $loc["label_location"] ?></th>
                    <th><?php echo $loc["label_note"] ?></th>
                </tr>

                <?php echo $arbeit->notifications()->get_notifications_edit_html(); ?>
            </table>
        </div>
    </body>
</html>