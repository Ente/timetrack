<?php

require "../inc/db.inc.php";
session_start();

$id = $_GET["id"];

$sql = "SELECT * FROM kalender WHERE id = '{$id}';";
$res = mysqli_query($conn, $sql);
$count = mysqli_num_rows($res);

if($count == 1){
    $data = mysqli_fetch_assoc($res);
    $datum = strftime("%d.%m.%Y", strtotime($data["datum"]));
} else {
    die("Kalendereintrag konnte nicht gefunden werden!");
}

?>
<!DOCTYPE html>

<html>
    <head>
        <title>Kalendereintrag | Malermeister Kleinod</title>
        <link rel="stylesheet" type="text/css" href="/assets/css/index.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>

    <body>
        <h2>Kalendereintrag vom <?php echo $datum; ?></h2>

        <div class="box">
            <p><b>Ort:</b> <?php  echo $data["ort"];  ?></p>
            <p><b>Datum:</b> <?php echo $datum; ?></p>
            <p><b>Uhrzeit:</b> <?php echo $data["uhrzeit"]; ?></p>
            <p><b>Notiz:</b><br> <?php echo $data["notiz"]; ?></p>
        </div>
    </body>
</html>