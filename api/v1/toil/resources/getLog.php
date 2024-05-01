<?php

require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Exceptions;

$ar = new Arbeitszeit();
header('Content-Type: text/plain');

$log_contents = @file_get_contents(Exceptions::logrotate()) ?? "Error retrieving log file!";
echo $log_contents;

?>