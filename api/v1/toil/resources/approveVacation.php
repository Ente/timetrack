<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
use Arbeitszeit\Vacation;
$vacation = new Vacation;
header('Content-Type: application/json');


$id = $_GET["id"] ?? die(json_encode(["error" => true]));
if($id != null){
    if($vacation->change_status($id, 1)){
        echo json_encode(["id" => $id]);
    } else {
        echo json_encode(["error" => true]);
    }
}