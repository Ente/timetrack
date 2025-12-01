<?php
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;

$arbeit = new Arbeitszeit;
$base_url = $arbeit->get_app_ini()["general"]["base_url"];
$arbeit->auth()->login_validation();

if(!isset($_POST["item_id"])){
    header("Location: http://{$base_url}/suite/?" . $arbeit->statusMessages()->URIBuilder("error"));
}

if($arbeit->benutzer()->current_user_is_admin()){
    if($arbeit->projects()->deleteItem($_GET["id"])){
        $arbeit->statusMessages()->redirect("success");
    } else {
        $arbeit->statusMessages()->redirect("error");
    }
}
