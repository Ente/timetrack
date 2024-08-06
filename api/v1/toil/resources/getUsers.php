<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
use Arbeitszeit\Arbeitszeit;

$sql = "SELECT username,id FROM `users`;";
$res = mysqli_query(Arbeitszeit::get_conn(), $sql);
header('Content-Type: application/json');
$arr = [];
if($res != false){
    while($row = mysqli_fetch_assoc($res)){
        $arr[$row["id"]] = $row["username"];
    }
    echo json_encode($arr);
    die();
} else {
    echo json_encode(["error" => true]);
}

?>