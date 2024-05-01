<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
use Arbeitszeit\Arbeitszeit;

$sql = "SELECT COUNT(id) FROM `users`;";
$res = mysqli_query(Arbeitszeit::get_conn(), $sql);
header('Content-Type: application/json');
if($res != false){
    echo json_encode(array("users" => mysqli_fetch_assoc($res)["COUNT(id)"]));
} else {
    echo json_encode(array(["error" => "Failed to retrieve user count."]));
}


?>