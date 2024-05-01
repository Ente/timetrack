<?php
require dirname(__DIR__, 2) . "/api/v1/inc/arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Kalender;
use Arbeitszeit\Auth;
use Arbeitszeit\Benutzer;
$user = new Benutzer;
$auth = new Auth;
$auth->login_validation();
$id = $_GET["id"];
$calendar = new Kalender;
$data = $calendar->get_calendar_entry($id);
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Kalendereintrag | <?php echo $ini["general"]["name"]; ?></title>
        <link rel="stylesheet" type="text/css" href="../../assets/css/index.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>

    <body>
        <h2>Kalendereintrag vom <?php echo strftime("%d.%m.%Y", strtotime($data["datum"])); ?></h2>
        <div class="box">
            <p><b>Ort:</b> <?php  echo $data["ort"];  ?></p>
            <p><b>Datum:</b> <?php echo $datum = strftime("%d.%m.%Y", strtotime($data["datum"])); ?></p>
            <p><b>Uhrzeit:</b> <?php echo $data["uhrzeit"]; ?></p>
            <p><b>Notiz:</b><br> <?php echo $data["notiz"]; ?></p>
        </div>

        <?php
        # Bug 13
        if($user->is_admin($user->get_user($_SESSION["username"]))){
            echo <<< DATA
            <a href="../admin/actions/calendar/delete.php?id={$_GET["id"]}">Löschen</a> | <span style="color:red">Dies lässt sich nicht rückgängig machen!</span>
            DATA;   
        }
        ?>
    </body>
</html>