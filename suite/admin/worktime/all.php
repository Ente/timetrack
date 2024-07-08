<?php
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Kalender;
use Arbeitszeit\Benutzer;
use Arbeitszeit\Auth;
use Arbeitszeit\i18n;
$i18n = new i18n;
$auth = new Auth;
$calendar = new Kalender;
$user = new Benutzer;
$arbeit = new Arbeitszeit;
$base_url = Arbeitszeit::get_app_ini()["general"]["base_url"];
$ini = Arbeitszeit::get_app_ini();
$loc = $i18n->loadLanguage(null, "worktime/all", "admin");
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
        <title><?php echo $loc["title"] ?> | <?php echo $ini["general"]["app_name"]; ?></title>
        <link rel="stylesheet" type="text/css" href="/assets/css/index.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/gui/standard_nav.php"; ?>
        <h1><?php echo $loc["title"] ?> | <?php echo $ini["general"]["app_name"]; ?></h1>
        <div class="box">
            <h2><?php echo $loc["h2"] ?></h2>
            <p><?php echo $loc["order"] ?></p>
            <form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="POST">
                <p><?php echo $loc["sorted"] ?></p>
                <label><?php echo $loc["year"] ?>:</label><input type="number" min="2020" max="2030" name="jahr" value="<?php echo $date_year ?>">
                <label><?php echo $loc["month"] ?>:</label><input type="number" min="01" max="12" name="monat" value="<?php echo $date_month ?>">
                <br>
                <button type="submit"><?php echo $loc["search"] ?></button>
            </form>

            <table style="width:100%;">
                <tr>
                    <th><?php echo $loc["employee"] ?></th>
                    <th><?php echo $loc["sday"] ?></th>
                    <th><?php echo $loc["sbegin"] ?></th>
                    <th><?php echo $loc["send"] ?></th>
                    <th><?php echo $loc["pbegin"] ?></th>
                    <th><?php echo $loc["pend"] ?></th>
                    <th><?php echo $loc["loc"] ?></th>
                </tr>

                <?php echo $arbeit->get_specific_worktime_html($date_month, $date_year)  ?>
            </table>
        </div>
    </body>
</html>