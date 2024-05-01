<?php
require dirname(__DIR__, 3) . "/api/v1/inc/arbeit.inc.php";
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
$base_url = Arbeitszeit::get_app_ini()["general"]["base_url"];
$ini = Arbeitszeit::get_app_ini();

$auth->login_validation();
if(!@$user->is_admin($_SESSION["username"])){
    header("Location http://{$base_url}/suite/?info=noperms");
}
$id = $_GET["id"];
$data = $calendar->get_calendar_entry($id);
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Kalendereintrag bearbeiten | <?php echo $ini["general"]["app_name"]; ?></title>
        <link rel="stylesheet" type="text/css" href="/assets/css/index.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <?php include dirname(__DIR__, 3). "/assets/gui/standard_nav.php" ?>

        <h1>Kalendereintrag bearbeiten</h1>
        <div class="box">
            <form action="../admin/actions/calendar/edit.php?id=<?php echo $id; ?>" method="POST">
                <label>Datum: </label><input type="date" name="datum" value="<?php echo $data["datum"] ?>">
                <br>
                <label>Uhrzeit: </label><input type="time" name="uhrzeit" value="<?php echo $data["uhrzeit"]; ?>">
                <br>
                <label>Ort: </label><input type="text" name="ort" value="<?php echo $data["ort"]; ?>">
                <br>
                <label>Notiz: </label><input type="text" name="notiz" value="<?php echo $data["notiz"];  ?>">
                <br>
                <button type="submit" name="submit" class="button">Absenden</button>
            </form>
        </div>
    </body>
</html>