<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
use Arbeitszeit\Arbeitszeit;
$arbeit = new Arbeitszeit;

echo $arbeit->statusMessages()->hook();