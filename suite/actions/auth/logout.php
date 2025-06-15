<?php

require dirname(__DIR__, 3) . "/api/v1/inc/arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
$arbeit = new Arbeitszeit;
$base_url = $ini = $arbeit->get_app_ini()["general"]["base_url"];
$arbeit->auth()->login_validation();
if($arbeit->auth()->logout() == true){
    header("Location: http://{$base_url}/suite/?" . $arbeit->statusMessages()->URIBuilder("logged_out"));
} else {
    header("Location: http://{$base_url}/suite/?" . $arbeit->statusMessages()->URIBuilder("error"));
}

?>