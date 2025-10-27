<?php
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
use Arbeitszeit\Arbeitszeit;
$worktime = new Arbeitszeit;
$base_url = $worktime->get_app_ini()["general"]["base_url"];
$worktime->auth()->login_validation();
$sick = $worktime->sickness()->add_sickness(start: $_POST["date-start"], stop: $_POST["date-end"], user: $_SESSION["username"], type: $_POST["Stype"]);
if(!$sick){
    header("Location: http://{$base_url}/suite/?" . $worktime->statusMessages()->URIBuilder("error_sickness"));
} else {
    header("Location: http://{$base_url}/suite/?" . $worktime->statusMessages()->URIBuilder("sickness_added"));
}



?>