<?php

require "../inc/db.inc.php";
session_start();
if($_SESSION["logged_in"] == false || !isset($_SESSION["logged_in"])) {
    header("Location: ../../login.php?error=notloggedin");
};


if(mysqli_connect_error()) { # Verbindungsfehler
    die("Fehler: " . mysqli_connect_error() . "\nDeine Schicht wurde nicht begonnen. Bitte kontaktiere sofort deinen Chef!");
};

$time_start = $_POST["time_start"];
$time_end = $_POST["time_end"];
$location = $_POST["ort"];
$date = $_POST["date"];
$username = $_SESSION["username"];

$sql = "SELECT * FROM users WHERE username = '{$username}';";
$res = mysqli_query($conn ,$sql);
$count = mysqli_num_rows($res);

if($count == 1){
    $data = mysqli_fetch_assoc($res);
} else {
    echo "Nutzer konnte nicht gefunden werden";
    die();
}

$sql1 = "INSERT INTO `arbeitszeiten` (`name`, `id`, `email`, `username`, `schicht_tag`, `schicht_anfang`, `schicht_ende`, `ort`) VALUES ('{$data["name"]}', '0', '{$data["email"]}', '{$username}', '{$date}', '{$time_start}', '{$time_end}', '{$location}');";
mysqli_query($conn, $sql1);
if(mysqli_error($conn)){
    die(mysqli_error($conn));
} else {
    header("Location: ../../../index.php?info=schicht_eingetragen");
}
?>