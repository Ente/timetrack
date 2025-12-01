<?php
require dirname(__DIR__, 4) . "/api/v1/inc/arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
$arbeit = new Arbeitszeit;
$base_url = $arbeit->get_app_ini()["general"]["base_url"];
$arbeit->auth()->login_validation();

if(!isset($_POST["project"], $_POST["userid"])){
    header("Location: http://{$base_url}/suite/?" . $arbeit->statusMessages()->URIBuilder("error"));
}

if($arbeit->projects()->checkUserisOwner($_POST["project"])){
    if($arbeit->projects()->addProjectMember($_POST["project"], $_POST["userid"], $_POST["permissions"], $_POST["role"])){
        header("Location: http://{$base_url}/suite/?" . $arbeit->statusMessages()->URIBuilder("project_userAdded"));
    } else {
        header("Location: http://{$base_url}/suite/?" . $arbeit->statusMessages()->URIBuilder("project_userAdded_failed"));
    }
}

?>