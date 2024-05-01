<?php
require "../../../api/v1/inc/arbeit.inc.php";
use Arbeitszeit\Auth;
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Benutzer;
$auth = new Auth();
$user = new Benutzer();
$worktime = new Arbeitszeit;
$base_url = $worktime->get_app_ini()["general"]["base_url"];

$data = $user->get_user($_SESSION["username"]);

$auth->login_validation();
if($user->is_admin($data) == true){
    echo "yes";
}


?>