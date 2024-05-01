<?php
require "../inc/db.inc.php"; # Datenbankverbindung

session_start();


if($_SESSION["logged_in"] == false || !isset($_SESSION["logged_in"])) {
    header("Location: ../../login.php?error=notloggedin");
};


if(mysqli_connect_error()) { # Verbindungsfehler
    die("Fehler: " . mysqli_connect_error() . "\nDeine Schicht wurde nicht begonnen. Bitte kontaktiere sofort deinen Chef!");
};

date_default_timezone_set("Europe/Berlin");
$time = time();
$date = date("d.m.Y");
$zeit = date("H:i");              # Speichert Datum, etc
$email = $_SESSION["email"];
setcookie("schicht_gestartet", true, $time+(10 * 365 * 24 * 60 * 60), "/") or die("Cookie konnte nicht gesetzt werden. Dein Browser muss Cookies unterstützen!\nDeine Schicht wurde nicht gestartet."); # Setzt Cookie

if(isset($_SESSION["email"])) {
$sql = "UPDATE schicht SET schicht_gestartet_zeit = '$zeit', schicht_datum = '$date' WHERE email = '$email';"; # Updated temporären Table
    if(!mysqli_query($conn, $sql)) {
        die("Fehler: " . mysqli_error($conn) . "\nDeine Schicht wurde unter Umständen nicht gestartet. Bitte kontaktiere deinen Chef!");
    } else {
        header("Location: ../../../index.php?info=schicht_begonnen");
    }

}


mysqli_close($conn);
?>