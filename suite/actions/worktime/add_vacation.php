<?php
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
#require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/class/vacation/vacation.arbeit.inc.php";
use Arbeitszeit\Auth;
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Benutzer;
use Arbeitszeit\Vacation;
$auth = new Auth();
$user = new Benutzer();
$worktime = new Arbeitszeit;
$vacation = new Vacation;
$base_url = Arbeitszeit::get_app_ini()["general"]["base_url"];
$auth->login_validation();
$sick = $vacation->add_vacation($_POST["date-start"], $_POST["date-end"]);
#echo var_dump($sick = $vacation->add_vacation($_POST["date-start"], $_POST["date-end"]));
#print_r($_POST);
#die();
if(!$sick){
    header("Location: http://{$base_url}/suite/?info=error_vacation");
} else {
    header("Location: http://{$base_url}/suite/?info=vacation_added");
}



?>