<?php
require dirname(__DIR__, 4) . "/api/v1/inc/arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Kalender;
use Arbeitszeit\Benutzer;
use Arbeitszeit\Auth;
use Arbeitszeit\Vacation;
$auth = new Auth;
$calendar = new Kalender;
$user = new Benutzer;
$arbeit = new Arbeitszeit;
$vacation = new Vacation;
$base_url = Arbeitszeit::get_app_ini()["general"]["base_url"];
$ini = Arbeitszeit::get_app_ini();

$auth->login_validation();
if(!$user->is_admin($user->get_user($_SESSION["username"]))){
    header("Location: http://{$base_url}/suite/?info=noperms");
}
if(!is_string(@$_POST["jahr"]) || !is_string(@$_POST["monat"])){
    $date_year = date("Y");
    $date_month = date("m");
} else {
    $date_year = $_POST["jahr"];
    $date_month = $_POST["monat"];
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Alle Urlaube | <?php echo $ini["general"]["app_name"]; ?></title>
        <link rel="stylesheet" type="text/css" href="../../../../assets/css/index.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <?php include dirname(__DIR__, 4) . "/assets/gui/standard_nav.php"; ?>
        <h1>Alle Urlaube | <?php echo $ini["general"]["app_name"]; ?></h1>
        <div class="box">
            <h2>Unten siehst du eine Liste aller Urlaube deiner Mitarbeiter.</h2>
            <p>Geordnet: Neu zu alt, die letzten 100 Einträge</p>

            <table style="width:100%;">
                <tr>
                    <th>Mitarbeiter</th>
                    <th>Urlaub Beginn</th>
                    <th>Urlaub Ende</th>
                    <th>Status</th>
                </tr>

                <?php echo $vacation->display_vacation_all()  ?>
            </table>
        </div>
    </body>
</html>