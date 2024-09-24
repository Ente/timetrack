<?php
require dirname(__DIR__, 4) . "/api/v1/inc/arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
$arbeit = new Arbeitszeit;
$ini = $arbeit->get_app_ini();
$base_url = $ini["general"]["base_url"];
$loc = $arbeit->i18n()->loadLanguage(null, "worktime/vacation/all", "admin");
$arbeit->auth()->login_validation();
if(!$arbeit->benutzer()->is_admin($arbeit->benutzer()->get_user($_SESSION["username"]))){
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
        <title><?php echo $loc["title"] ?> | <?php echo $ini["general"]["app_name"]; ?></title>
        <link rel="stylesheet" type="text/css" href="../../../../assets/css/index.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <?php include dirname(__DIR__, 4) . "/assets/gui/standard_nav.php"; ?>
        <h1><?php echo $loc["title"] ?> | <?php echo $ini["general"]["app_name"]; ?></h1>
        <div class="box">
            <h2><?php echo $loc["note1"] ?></h2>
            <p><?php echo $loc["note2"] ?></p>

            <table style="width:100%;">
                <tr>
                    <th><?php echo $loc["t1"] ?></th>
                    <th><?php echo $loc["t2"] ?></th>
                    <th><?php echo $loc["t3"] ?></th>
                    <th><?php echo $loc["t4"] ?></th>
                </tr>

                <?php echo $arbeit->vacation()->display_vacation_all()  ?>
            </table>
        </div>
    </body>
</html>