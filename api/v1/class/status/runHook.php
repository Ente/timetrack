<?php
require_once dirname(__DIR__, 4) . "/api/v1/inc/arbeit.inc.php";
use Arbeitszeit\Arbeitszeit;
$arbeit = new Arbeitszeit;

echo $arbeit->statusMessages()->hook();