<?php
require "../../../api/v1/inc/arbeit.inc.php";
use Arbeitszeit\Auth;
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Benutzer;
$auth = new Auth();
$user = new Benutzer();
$worktime = new Arbeitszeit;
$base_url = Arbeitszeit::get_app_ini()["general"]["base_url"];
$auth->login_validation();

$t = $worktime->toggle_easymode($_SESSION["username"]);

if($t == true){
    header("Location: http://{$base_url}/suite/?info=easymode_toggled");
    die();
}

header("")



?>