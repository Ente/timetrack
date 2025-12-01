<?php
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
session_start();

use Arbeitszeit\Arbeitszeit;

$arbeit = new Arbeitszeit;
$base_url = $arbeit->get_app_ini()["general"]["base_url"];
$arbeit->auth()->login_validation();

// Check required POST param
if(!isset($_POST["id"])) {
    header("Location: http://{$base_url}/suite/?" . 
        $arbeit->statusMessages()->URIBuilder("error"));
    exit;
}

$item_id = $_POST["id"];

if (!$arbeit->benutzer()->current_user_is_admin() &&
    !$arbeit->projects()->checkUserisOwner($arbeit->projects()->getItem($item_id)["pid"])) 
{
    header("Location: http://{$base_url}/suite/?" . 
        $arbeit->statusMessages()->URIBuilder("forbidden"));
    exit;
}

$changes = [
    "title"       => $_POST["title"] ?? "",
    "description" => $_POST["description"] ?? "",
    "assignee"    => $_POST["assignee"] ?? null,
];

$res = $arbeit->projects()->editItem($item_id, $changes);

if ($res) {
    header("Location: http://{$base_url}/suite/projects/item.php?id={$item_id}&" . 
        $arbeit->statusMessages()->URIBuilder("success"));
    exit;
} else {
    header("Location: http://{$base_url}/suite/projects/item.php?id={$item_id}&" .  
        $arbeit->statusMessages()->URIBuilder("error"));
    exit;
}
