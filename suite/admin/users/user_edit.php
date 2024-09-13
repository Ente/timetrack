<?php
session_start();
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
use Arbeitszeit\Arbeitszeit;
$arbeit = new Arbeitszeit;
$ini = $arbeit->get_app_ini();
$base_url = $ini["general"]["base_url"];

if(!isset($_POST["username"])){
    header("Location: http://{$base_url}/suite/?info=nouserid");
}

$auth->login_validation();
if(!$user->is_admin($user->get_user($_SESSION["username"]))){
    header("Location: http://{$base_url}/suite/?info=noperms");
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Benutzer bearbeiten | <?php echo $ini["general"]["app_name"]; ?></title>
        <link rel="stylesheet" type="text/css" href="/assets/css/index.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <?php  include $_SERVER["DOCUMENT_ROOT"] . "/assets/gui/standard_nav.php" ?>

        <h1>'<?php echo $_POST["username"]; ?>' bearbeiten</h1>
