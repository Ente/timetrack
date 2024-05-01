<?php

require "../inc/db.inc.php";


$email = urldecode($_GET["email"]);
$fake_all = $_GET["fake"];

if($fake_all == "true") {
    if(isset($email)) {
        $sql = "SELECT email FROM users WHERE email = '$email';"; # Überprüft ob Nutzer überhaupt existiert

        $res = mysqli_query($conn, $sql);
        $count = mysqli_fetch_row($res);
        if($count == 1) {

            $sql1 = "UPDATE users SET email_confirmed = 1 WHERE email = '$email';"; # Setzt Wert auf 1 
            if(!mysqli_query($conn, $sql1)) {
                die("Deine Email konnte nicht bestätigt werden, womöglich ein Fehler der Datenbank.\nFehlernachricht: " . mysqli_error($conn));
            } else {
                header("Location: ../../../login.php?success=email");
            }

        } else {
            die("Deine Email konnte nicht bestätigt werden (1).\nFehlernachricht: " . mysqli_error($conn) . "\n" . $email);
        };
    }
}


mysqli_close($conn);
?>