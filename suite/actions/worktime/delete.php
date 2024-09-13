<?php
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
use Arbeitszeit\Arbeitszeit;
$worktime = new Arbeitszeit;
$base_url = $worktime->get_app_ini()["general"]["base_url"];
$data = $arbeit->benutzer()->get_user($_SESSION["username"]);
$arbeit->auth()->login_validation();
if($arbeit->benutzer()->is_admin($data) == true){
    echo "yes";
}


?>