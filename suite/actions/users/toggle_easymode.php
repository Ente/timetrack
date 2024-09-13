<?php
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
use Arbeitszeit\Arbeitszeit;
$worktime = new Arbeitszeit;
$base_url = $arbeit->get_app_ini()["general"]["base_url"];
$arbeit->auth()->login_validation();
$t = $worktime->toggle_easymode($_SESSION["username"]);
if($t == true){
    header("Location: http://{$base_url}/suite/?info=easymode_toggled");
    die();
}
?>