<?php


require "../inc/db.inc.php";

$id = $_GET["id"];
$datum = $_POST["datum"];
$uhrzeit = $_POST["uhrzeit"];
$ort = $_POST["ort"];
$notiz = $_POST["notiz"];

$sql = "UPDATE `kalender` SET `datum` = '{$datum}', `uhrzeit` = '{$uhrzeit}', `ort` = '{$ort}', `notiz` = '{$notiz}';";
mysqli_query($conn, $sql);

if(mysqli_error($conn)){
    die(mysqli_error($conn));
} else {
    header("Location: ../../../../index.php?info=eintrag_bearbeitet");
}

?>