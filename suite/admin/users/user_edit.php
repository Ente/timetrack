<?php
session_start();
require dirname(__DIR__, 3)."/api/v1/inc/arbeit.inc.php";
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Kalender;
use Arbeitszeit\Benutzer;
use Arbeitszeit\Auth;

$auth = new Auth;
$calendar = new Kalender;
$user = new Benutzer;
$arbeit = new Arbeitszeit;
$base_url = Arbeitszeit::get_app_ini()["general"]["base_url"];
$ini = Arbeitszeit::get_app_ini();

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
        <?php  include "../../../assets/gui/standard_nav.php" ?>

        <h1>'<?php echo $_POST["username"]; ?>' bearbeiten</h1>
