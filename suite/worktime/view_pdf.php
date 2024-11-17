<?php
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
@session_start();
use Arbeitszeit\Arbeitszeit;
$arbeit = new Arbeitszeit;

$arbeit->auth()->login_validation();
echo $arbeit->exportModule()->export(array("module" => "PDFExportModule", "user" => $_GET["mitarbeiter"], "month" => $_GET["monat"], "year" => $_GET["jahr"]));
$arbeit->exportModule()->getExportModule("PDFExportModule")->saveAsPdf(array("user" => $_GET["mitarbeiter"], "month" => $_GET["monat"], "year" => $_GET["jahr"]));
?>