<?php
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
        <title>Alle Arbeitszeiten | <?php echo $ini["general"]["app_name"]; ?></title>
        <link rel="stylesheet" type="text/css" href="../../../assets/css/index.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <?php include dirname(__DIR__, 3) . "/assets/gui/standard_nav.php"; ?>
        <h1>Alle Arbeitszeiten | <?php echo $ini["general"]["app_name"]; ?></h1>
        <div class="box">
            <h2>Unten siehst du eine Liste aller Arbeitszeiten deiner Mitarbeiter.</h2>
            <p>Geordnet: Neu zu alt</p>
            <form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="POST">
                <p>Sortieren nach:</p>
                <label>Jahr:</label><input type="number" min="2020" max="2030" name="jahr" value="<?php echo $date_year ?>">
                <label>Monat:</label><input type="number" min="01" max="12" name="monat" value="<?php echo $date_month ?>">
                <br>
                <button type="submit">Suchen</button>
            </form>

            <table style="width:100%;">
                <tr>
                    <th>Mitarbeiter</th>
                    <th>Schicht Tag</th>
                    <th>Schicht Anfang</th>
                    <th>Schicht Ende</th>
                    <th>Pause Start</th>
                    <th>Pause Ende</th>
                    <th>Ort</th>
                </tr>

                <?php echo $arbeit->get_specific_worktime_html($date_month, $date_year)  ?>
            </table>
        </div>
    </body>
</html>