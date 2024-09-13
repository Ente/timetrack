<?php
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
use Arbeitszeit\Arbeitszeit;
$worktime = new Arbeitszeit;
$base_url = $arbeit->get_app_ini()["general"]["base_url"];
$arbeit->auth()->login_validation();
$vacation = $arbeit->vacation()->add_vacation($_POST["date-start"], $_POST["date-end"]);
if(!$vacation){
    header("Location: http://{$base_url}/suite/?info=error_vacation");
} else {
    header("Location: http://{$base_url}/suite/?info=vacation_added");
}



?>