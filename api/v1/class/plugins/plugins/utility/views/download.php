<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/class/plugins/loader.plugins.arbeit.inc.php";
require_once dirname(__DIR__, 1) . "/src/Main.php";

use Arbeitszeit\Arbeitszeit;
use utility\utility;
use Arbeitszeit\Benutzer;
use Arbeitszeit\Exceptions;

$a = new Arbeitszeit;
$benutzer = new Benutzer;
$main = new utility;

$a->auth()->login_validation();
$a->benutzer()->current_user_is_admin();

if(!isset($_POST["username"])){
    $main->logger("[utility] Username not found. Aborting export...");
    $a->statusMessages()->redirect("error");
}

$main->exportAll($_POST["username"])->download();