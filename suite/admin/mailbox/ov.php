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
$from = (!isset($_POST["f"])) ? 1 : (int) $_POST["f"];
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Alle Mailbox-Einträge | <?php echo $ini["general"]["app_name"]; ?></title>
        <link rel="stylesheet" type="text/css" href="../../../assets/css/index.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <?php include dirname(__DIR__, 3) . "/assets/gui/standard_nav.php"; ?>
        <h1>Alle Mailbox-Einträge | <?php echo $ini["general"]["app_name"]; ?></h1>
        <div class="box">
            <h2>Unten siehst du eine Liste aller Mailbox-Einträge.</h2>
            <p><a href="http://<?php echo $base_url ?>/suite/admin/mailbox/add.php">Mailbox-Eintrag hinzufügen</a></p>
            <p>Geordnet: Alt zu neu</p>
            <form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="POST">
                <p>Sortieren nach:</p>
                <label>Von:</label><input type="number" min="1"  name="f" value="1">
                <br>
                <button type="submit">Suchen</button>
            </form>

            <table style="width:100%;">
                <tr>
                    <th>Aktion</th>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Datei</th>
                    <th>Benutzer</th>
                    <th>Gesehen</th>
                </tr>

                <?php echo $mb->get_specific_mailbox_html($from)  ?>
            </table>
        </div>
    </body>
</html>