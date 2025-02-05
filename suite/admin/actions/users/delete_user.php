<?php
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
$arbeit = new Arbeitszeit;
$id = $_GET["id"];
$ini = $arbeit->get_app_ini();
$base_url = $ini["general"]["base_url"];
$arbeit->auth()->login_validation();
if($arbeit->benutzer()->is_admin($arbeit->benutzer()->get_user($_SESSION["username"]))){
    if($arbeit->benutzer()->get_user_from_id($id)["username"] == "api"){
        header("Location: http://{$base_url}/suite/?info=error");
        die();
    }
    if($arbeit->benutzer()->delete_user($id) == true){
        header("Location: http://{$base_url}/suite/?info=user_deleted");
    }  
} else {
    header("Location http://{$base_url}/suite/?info=noperms");
}
?>