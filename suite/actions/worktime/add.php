<?php
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
use Arbeitszeit\Arbeitszeit;
$worktime = new Arbeitszeit;
$base_url = $worktime->get_app_ini()["general"]["base_url"];
$worktime->auth()->login_validation();
$work = $worktime->add_worktime($_POST["time_start"], $_POST["time_end"], $_POST["ort"], $_POST["date"], $_POST["username"],"worktime", array("start" => $_POST["pause_start"], "end" => $_POST["pause_end"]));
if(!$work){
    header("Location: http://{$base_url}/suite/?info=error_worktime");
} else {
    header("Location: http://{$base_url}/suite/?info=worktime_added");
}



?>