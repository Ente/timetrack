<?php
require "../inc/db.inc.php";
session_start();
if($_SESSION["logged_in"] == false || !isset($_SESSION["logged_in"])) {
    header("Location: ../../login.php?error=notloggedin");
};


if(mysqli_connect_error()) { # Verbindungsfehler
    die("Fehler: " . mysqli_connect_error() . "\nDeine Schicht wurde nicht begonnen. Bitte kontaktiere sofort deinen Chef!");
};

$id = $_GET["id"];
$sql = "DELETE FROM kalender WHERE id = '{$id}';";
mysqli_query($conn, $sql);
if(mysqli_error($conn)){
    die(mysqli_error($conn));
} else {
    header("Location: ../../../../index.php?info=kalender_del");
}

?>