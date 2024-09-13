<?php
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
use Arbeitszeit\Arbeitszeit;
$worktime = new Arbeitszeit;
$base_url = $arbeit->get_app_ini()["general"]["base_url"];
$arbeit->auth()->login_validation();
$sick = $arbeit->sickness()->add_sickness($_POST["date-start"], $_POST["date-end"]);
if(!$sick){
    header("Location: http://{$base_url}/suite/?info=error_sickness");
} else {
    header("Location: http://{$base_url}/suite/?info=sickness_added");
}



?>