<?php
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
use Arbeitszeit\Arbeitszeit;
session_start();
$arbeit = new Arbeitszeit;
$arbeit->auth()->login_validation();
if(!$arbeit->exportModule()->export(array("module" => "CSVExportModule","year" => $_GET["jahr"], "month" => $_GET["monat"], "user" => $_GET["mitarbeiter"]))){
    echo "No Data found.";
}
?>