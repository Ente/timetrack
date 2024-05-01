<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\License;

$ar = new Arbeitszeit();
$l = new License();
header('Content-Type: application/json');
try {
    $e = json_encode($l->compute_license());
    echo $e;
} catch(\Exception $e){
    echo $e->getMessage();
    echo $e->getTraceAsString();
}

?>