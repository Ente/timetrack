<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
use Arbeitszeit\Vacation;
$vacation = new Vacation;
header('Content-Type: application/json');
$vacationdat = $vacation->get_all_vacation();
if($vacationdat != false){
    echo json_encode($vacationdat);
} else {
    echo json_encode(["error" => true]);
}
