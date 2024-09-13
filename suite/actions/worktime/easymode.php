<?php
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
use Arbeitszeit\Arbeitszeit;
$worktime = new Arbeitszeit;
$base_url = $arbeit->get_app_ini()["general"]["base_url"];
$arbeit->auth()->login_validation();

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