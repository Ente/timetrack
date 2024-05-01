<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
header('Content-Type: application/json');
$v = @file_get_contents("https://git.openducks.org/bryan/fileshare/-/raw/main/asze/version.txt") or "Failed to retrieve version from server.";
$v = str_replace("\n", "", $v);
echo json_encode(array("version" => $v));

?>