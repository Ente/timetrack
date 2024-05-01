<?php

require "../inc/db.inc.php";

session_start();

if($_SESSION["logged_in"] == false || !isset($_SESSION["logged_in"])) {
    header("Location ../../login.php?error=notloggedin");
};

if(mysqli_connect_error()) {
    die("Fehler: " . mysqli_connect_error() . "\nDeine Schicht wurde nicht beendet. Bitte kontaktiere sofort deinen Chef.");
};

date_default_timezone_set("Europe/Berlin");
$time = time();
$date = date("d.m.Y");
$zeit = date("H:i");
$username = $_SESSION["username"];
setcookie("schicht_beendet", true, $time+(10 * 365 * 24 * 60 * 60), "/") or die("Cookie konnte nicht gesetzt werden. Dein Browser muss Cookies unterstützen.\nDeine Schicht wurde nicht beendet.");
setcookie("schicht_gestartet", "nein", $time-(3600*200), "/");

if(isset($_SESSION["email"])) {
    $sql = "UPDATE schicht set schicht_ende_zeit = '$zeit' WHERE username = '$username';";
    if(!mysqli_query($conn, $sql)) {
        die("Fehler: " . mysqli_error($conn) . "\nDeine Schicht wurde unter Umständen nicht beendet. Bitte kontaktiere deinen Chef.");
    };

    $sql1 = "SELECT * FROM schicht WHERE username = '$username';";

    $res = mysqli_query($conn, $sql1);
    $count = mysqli_num_rows($res);

    if($count == 1) {
        $sql2 = "SELECT * FROM schicht WHERE username = '$username';";
        $res_all = mysqli_query($conn, $sql2);
        while($row = mysqli_fetch_array($res_all)) {
            $name = $row["name"];
            $username = $row["username"];
            $schicht_gestartet_zeit = $row["schicht_gestartet_zeit"];
            $schicht_ende_zeit = $row["schicht_ende_zeit"];
            $schicht_datum = $row["schicht_datum"];

            $sql3 = "INSERT INTO arbeitszeiten (name, username, schicht_tag, schicht_anfang, schicht_ende) VALUES ('$name', '$email', '$schicht_datum', '$schicht_gestartet_zeit', '$schicht_ende_zeit');";

            if(!mysqli_query($conn, $sql3)) {
                die("Fehler: " . mysqli_error($conn) . "\nDeine Schicht wurde unter Umständen nicht beendet. Bitte kontaktiere deinen Chef.");
            } else {
                header("Location: ../../../index.php?info=schicht_beendet");
                print_r("Du solltest gleicht weitergeleitet werden!");
            }

        }
    }
}
mysqli_close($conn);
?>