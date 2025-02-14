<?php
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
$arbeit = new Arbeitszeit;
$ini = $arbeit->get_app_ini();
$base_url = $ini["general"]["base_url"];
$loc = $arbeit->i18n()->loadLanguage(null, "worktime/all", "admin");
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
                <label><?php echo $loc["year"] ?>:</label><input class="input" type="number" min="2020" max="2030" name="jahr" value="<?php echo $date_year ?>">
                <label><?php echo $loc["month"] ?>:</label><input class="input" type="number" min="01" max="12" name="monat" value="<?php echo $date_month ?>">
                <br>
                <button class="button" type="submit"><?php echo $loc["search"] ?></button>
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