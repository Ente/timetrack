<?php
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
@session_start();
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Kalender;
use Arbeitszeit\Benutzer;
use Arbeitszeit\Auth;
use Arbeitszeit\pdf;

$username = $_SESSION["username"];
$auth = new Auth;
$calendar = new Kalender;
$user = new Benutzer;
$arbeit = new Arbeitszeit;
$pdf = new pdf;
$base_url = $ini = Arbeitszeit::get_app_ini();

$auth->login_validation();
echo $pdf->get_specific_worktime_pdf($_GET["mitarbeiter"], $_GET["monat"], $_GET["jahr"]);
?>