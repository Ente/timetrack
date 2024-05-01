<?php
require dirname(__DIR__, 2) . "/api/v1/inc/arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Kalender;
use Arbeitszeit\Benutzer;
use Arbeitszeit\Auth;
$username = $_SESSION["username"];
$auth = new Auth;
$calendar = new Kalender;
$user = new Benutzer;
$arbeit = new Arbeitszeit;
$base_url = $ini = Arbeitszeit::get_app_ini();

$auth->login_validation();
$date_year = date("Y");
$date_month = date("m");
if(!isset($_GET["jahr"])){
    $jahr = $date_year;
}
if(!isset($_GET["monat"])){
    $monat = $date_month;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Eigene Arbeitszeiten | <?php echo $ini["general"]["app_name"]; ?></title>
        <link rel="stylesheet" type="text/css" href="../../../assets/css/index.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <?php include "../../assets/gui/standard_nav.php"; ?>
        <h1>Eigene Arbeitszeiten | <?php echo $ini["general"]["app_name"]; ?></h1>
        <div class="box">
            <h2>Liste deiner Schichten</h2>

            <table style="width:100%;">
                <tr>
                    <th>Schicht Tag</th>
                    <th>Schicht Anfang</th>
                    <th>Schicht Ende</th>
                    <th>Pause Start</th>
                    <th>Pause Ende</th>
                    <th>Ort</th>
                </tr>

                <?php echo $arbeit->get_employee_worktime_html($username)  ?>
            </table>
        </div>
    </body>
</html>