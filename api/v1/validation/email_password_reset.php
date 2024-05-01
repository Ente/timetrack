<?php

require "../inc/db.inc.php";

session_start();

$email = urldecode($_GET["email"]);

$fake = $_GET["fake_auth"];

if($fake == "true") {
    if(isset($email)) {
        $sql = "SELECT email FROM users WHERE email = '$email';";

        $res = mysqli_query($conn, $sql);
        $count = mysqli_fetch_row($res);

        if($count == 1) {
            $sql1 = "UPDATE users SET password = '123456789' WHERE email = '$email';";
            if(!mysqli_query($conn, $sql1)) {
                die("Dein Passwort wurde nicht zurückgesetzt, womöglich ein Fehler der Datenbank.\nFehlernachricht: " . mysqli_error($conn));

            } else {
                header("Location: ../../../login.php?success=password_reset");
            };
        } else {
            die("Deine Email wurde nicht gefunden. Bitte melde dich bei deinem Chef!");
        };
    }
}

mysqli_close($conn);

?>