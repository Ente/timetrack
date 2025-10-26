<?php
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
use Arbeitszeit\Arbeitszeit;
$worktime = new Arbeitszeit;
$base_url = $worktime->get_app_ini()["general"]["base_url"];
$worktime->auth()->login_validation();
$vacation = $worktime->vacation()->add_vacation(start: $_POST["date-start"], stop: $_POST["date-end"], username: $_SESSION["username"], type: $_POST["Vtype"]);
if(!$vacation){
    header("Location: http://{$base_url}/suite/?" . $worktime->statusMessages()->URIBuilder('error_vacation'));
} else {
    header("Location: http://{$base_url}/suite/?" . $worktime->statusMessages()->URIBuilder('vacation_added'));
}



?>