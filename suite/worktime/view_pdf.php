<?php
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
@session_start();
use Arbeitszeit\Arbeitszeit;
$arbeit = new Arbeitszeit;

$arbeit->auth()->login_validation();
echo $arbeit->pdf()->get_specific_worktime_pdf($_GET["mitarbeiter"], $_GET["monat"], $_GET["jahr"]);
?>