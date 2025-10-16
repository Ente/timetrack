<?php
require dirname(__DIR__, 4) . "/api/v1/inc/arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
$arbeit = new Arbeitszeit;
$base_url = $arbeit->get_app_ini()["general"]["base_url"];
$arbeit->auth()->login_validation();

if(!isset($_POST["projectId"], $_POST["userId"])){
    header("Location: http://{$base_url}/suite/?" . $arbeit->statusMessages()->URIBuilder("error"));
}

if($arbeit->projects()->checkUserisOwner($_POST["projectId"])){
    if($arbeit->projects()->addProjectMember($_POST["projectId"], $_POST["userId"], $_POST["permissions"], $_POST["role"])){
        header("Location: http://{$base_url}/suite/?" . $arbeit->statusMessages()->URIBuilder("project_userAdded"));
    } else {
        header("Location: http://{$base_url}/suite/?" . $arbeit->statusMessages()->URIBuilder("project_userAdded_failed"));
    }
}

?>