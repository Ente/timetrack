<?php


require "../inc/db.inc.php";

$name = $_POST["name"];
$username = $_POST["username"];
$email = $_POST["email"];

if(isset($_POST["password"])){
    $password = $_POST["password"];
} else {
    $password = "123456789";
}

$sql = "INSERT INTO `users` (`name`, `username`, `email`, `password`, `email_confirmed`) VALUES ('{$name}', '{$username}', '{$email}', '{$password}', '1');";
mysqli_query($conn, $sql);

if(mysqli_error($conn)){
    die(mysqli_error($conn));
} else {
    header("Location: ../../../../index.php?info=benutzer_hinzugefuegt");
}
?>