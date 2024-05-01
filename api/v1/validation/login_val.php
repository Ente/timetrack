<?php

include "../inc/db.inc.php"; # Lädt Datenbankverbindungsdatei

session_start();
$conn = mysqli_connect($db_host, $db_username, $db_password, $db);

if(!mysqli_connect_error()) { # Fehler bei Verbindung
    if(!isset($_POST["username"]) || !isset($_POST["password"])) { # Checkt ob überhaupt eine Email und ein Passwort eingegeben wurde
        die(header("Location: ../../../login.php?error=nodata"));
    } else {

        $username = $_POST["username"];
        $password = $_POST["password"];

        $sql = "SELECT name, password FROM users WHERE username = '{$username}' AND password = '$password'; "; # Vergleicht Daten mit der Datenbank
        $res = mysqli_query($conn, $sql);
        $count = mysqli_num_rows($res);
        if($count == 1) {
            $ts = time();
            $_SESSION["logged_in"] = true;
            $_SESSION["username"] = $username;
            $_SESSION["time"] = date("d.m.Y H:i:s", $ts);

            if(isset($_POST["erinnern"])) {
                setcookie("erinnern", "true", $ts+(60*60*24*30), "/");
                setcookie("username", $username, $ts + (60*60*24*30), "/");
            }
                    header("Refresh: 1; url=../../../index.php");
        } else {
            die(header("Location: ../../../login.php?error=wrongdata"));
        }
    }
} else {
    die("Error: " . mysqli_connect_error());
}



?>