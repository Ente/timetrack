<?php
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Kalender;
use Arbeitszeit\Benutzer;
use Arbeitszeit\Auth;
use Arbeitszeit\i18n;
$i18n = new i18n;
$username = $_SESSION["username"];
$auth = new Auth;
$calendar = new Kalender;
$user = new Benutzer;
$arbeit = new Arbeitszeit;
$base_url = $ini = Arbeitszeit::get_app_ini();
$loc = $i18n->loadLanguage(NULL, "worktime/all");
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
        <title><?php echo $loc["title"] ?> | <?php echo $ini["general"]["app_name"]; ?></title>
        <link rel="stylesheet" type="text/css" href="/assets/css/index.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/gui/standard_nav.php"; ?>
        <h1><?php echo $loc["title"] ?> | <?php echo $ini["general"]["app_name"]; ?></h1>
        <div class="box">
            <h2><?php echo $loc["h2"] ?></h2>

            <table style="width:100%;">
                <tr>
                    <th><?php echo $loc["s_day"] ?></th>
                    <th><?php echo $loc["s_begin"] ?></th>
                    <th><?php echo $loc["s_end"] ?></th>
                    <th><?php echo $loc["s_pstart"] ?></th>
                    <th><?php echo $loc["s_pend"] ?></th>
                    <th><?php echo $loc["s_location"] ?></th>
                </tr>

                <?php echo $arbeit->get_employee_worktime_html($username)  ?>
            </table>
        </div>
    </body>
</html>