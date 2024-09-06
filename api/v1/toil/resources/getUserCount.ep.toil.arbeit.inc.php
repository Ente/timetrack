<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\DB;
$db = new DB;
$sql = "SELECT COUNT(id) FROM `users`;";
$res = $db->simpleQuery($sql)->execute();
header('Content-Type: application/json');
if($res){
    echo json_encode(array("users" => $res->fetch(\PDO::FETCH_ASSOC)["COUNT(id)"]));
} else {
    echo json_encode(array(["error" => "Failed to retrieve user count."]));
}


?>