<?php
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
#require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/class/sickness/sickness.arbeit.inc.php";
use Arbeitszeit\Auth;
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Benutzer;
use Arbeitszeit\Sickness;
$auth = new Auth();
$user = new Benutzer();
$worktime = new Arbeitszeit;
$sickness = new Sickness;
$base_url = Arbeitszeit::get_app_ini()["general"]["base_url"];
$auth->login_validation();
#echo var_dump($sick = $sickness->add_sickness($_POST["date-start"], $_POST["date-end"]));
#print_r($_POST);
#die();
$sick = $sickness->add_sickness($_POST["date-start"], $_POST["date-end"]);
if(!$sick){
    header("Location: http://{$base_url}/suite/?info=error_sickness");
} else {
    header("Location: http://{$base_url}/suite/?info=sickness_added");
}



?>