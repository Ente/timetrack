<?php

require dirname(__DIR__, 3) . "/api/v1/inc/arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Kalender;
use Arbeitszeit\Benutzer;
use Arbeitszeit\Auth;
$username = $_SESSION["username"];
$auth = new Auth;
$calendar = new Kalender;
$user = new Benutzer;
$arbeit = new Arbeitszeit;
$base_url = $ini = Arbeitszeit::get_app_ini()["general"]["base_url"];
$auth->login_validation();
if($auth->logout() == true){
    header("Location: http://{$base_url}/suite/?info=logged_out");
} else {
    header("Location: http://{$base_url}/suite/?info=logged_out_e");
}

?>