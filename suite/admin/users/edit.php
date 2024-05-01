<?php
session_start();
require dirname(__DIR__, 3)."/api/v1/inc/arbeit.inc.php";
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Kalender;
use Arbeitszeit\Benutzer;
use Arbeitszeit\Auth;
use Arbeitszeit\License;

$auth = new Auth;
$calendar = new Kalender;
$user = new Benutzer;
$arbeit = new Arbeitszeit;
$lic = new License;
$base_url = Arbeitszeit::get_app_ini()["general"]["base_url"];
$ini = Arbeitszeit::get_app_ini();

$slot_data = $lic->calculate_users_html();
$html_slots = "<div style='font-size:x-small;'>{$slot_data["used"]} von {$slot_data["slots"]} Benutzern verwendet. {$slot_data["free"]} frei. <br><progress max='100' value='{$slot_data["percent"]}'></progress>' </div>";

$auth->login_validation();
if(!$user->is_admin($user->get_user($_SESSION["username"]))){
    header("Location: http://{$base_url}/suite/?info=noperms");
}
$add_style = "";
if($lic->validate() != true){
   $add_style = "disable";
}

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Benutzer bearbeiten | <?php echo $ini["general"]["app_name"]; ?></title>
        <link rel="stylesheet" type="text/css" href="/assets/css/index.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <?php  include "../../../assets/gui/standard_nav.php" ?>

        <h1>Benutzer bearbeiten</h1>
        <div class="box">
            <p>Hier kannst du alle Nutzer bearbeiten oder neue hinzufügen oder löschen</p>
            <h2>Benutzer löschen</h2>
                <table style="width:100%;">
                    <tr>
                        <th>Aktion</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Email</th>
                    </tr>
                    <?php $user->get_all_users_html(); ?>
                </table>

                <h2>Benutzer hinzufügen</h2>
                <?php echo $html_slots;  ?>
                <form action="../../admin/actions/users/add_user.php" method="POST">
                    <label title="Der Username besteht aus Anfangsbuchstabe des Vornamens großgeschrieben und voller Nachname, mit großem Anfangsbuchstaben. Beispiel: MMueller">Username: </label><input type="text" name="username" placeholder="MMustermann">
                    <br>
                    <label>Vorname:</label><input type="text" name="name" placeholder="Vorname des Angestellten">
                    <br>
                    <label>Email:</label><input type="email" name="email" required placeholder="Email des Angestellten">
                    <br>
                    <label>Passwort:</label><input type="password" name="password">
                    <br>
                    <label>Admin?</label><input type="checkbox" value="true" name="admin">
                    <button type="submit" name="submit" class="button" <?php echo $add_style;  ?>>Absenden</button>
                </form>
        </div>
    </body>
</html>