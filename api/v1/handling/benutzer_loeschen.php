<?php


require "../inc/db.inc.php";

$id = $_GET["id"];

$sql = "DELETE FROM `users` WHERE id = '{$id}';";
mysqli_query($conn, $sql);

if(mysqli_error($conn)){
    die(mysqli_error($conn));
} else {
    header("Location: ../../../../index.php?info=benutzer_geloescht");
}




?>