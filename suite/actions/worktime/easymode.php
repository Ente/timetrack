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

if($_POST["type"] == "start"){
    $work = $worktime->add_easymode_worktime($_SESSION["username"]);
    header("Location: http://{$base_url}/suite/?info=worktime_easymode_start");
    die();
} elseif($_POST["type"] == "stop"){
    $work = $worktime->end_easymode_worktime($_SESSION["username"], $_POST["id"]);
    header("Location: http://{$base_url}/suite/?info=worktime_easymode_end");
    die();
} elseif($_POST["type"] == "pause_start"){
    $work = $worktime->start_easymode_pause_worktime($_SESSION["username"], $_POST["id"]);
    header("Location: http://{$base_url}/suite/?info=worktime_easymode_pause_start");
    die();
} elseif($_POST["type"] == "pause_end"){
    $work = $worktime->end_easymode_pause_worktime($_SESSION["username"], $_POST["id"]);
    header("Location: http://{$base_url}/suite/?info=worktime_easymode_pause_end");
    die();
}

header("Location: http://{$base_url}/suite/?info=error");
die();



?>