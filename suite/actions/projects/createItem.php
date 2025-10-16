<?php
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
$arbeit = new Arbeitszeit;
$base_url = $arbeit->get_app_ini()["general"]["base_url"];
$arbeit->auth()->login_validation();

if(!isset($_POST["title"], $_POST["description"], $_POST["project_id"])){
    $arbeit->statusMessages()->redirect("error");
}

if(($arbeit->projects()->checkUserisOwner($_POST["project_id"]) || $arbeit->projects()->checkUserHasProjectAccess($arbeit->benutzer()->get_current_user()["id"], 0))){
    if($arbeit->projects()->addProjectItem($_POST["project_id"], $_POST["title"], $_POST["description"], $_POST["assignee"])){
        $arbeit->statusMessages()->redirect("projects_item_added");
    } else {
        $arbeit->statusMessages()->redirect("project_item_failed");
    }
}