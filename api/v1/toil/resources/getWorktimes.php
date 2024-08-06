<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
use Arbeitszeit\Arbeitszeit;
$arbeit = new Arbeitszeit;
header('Content-Type: application/json');
$worktimes = $arbeit->get_all_worktime();
if($worktimes != false){
    echo json_encode($worktimes);
} else {
    echo json_encode(["error" => true]);
}
