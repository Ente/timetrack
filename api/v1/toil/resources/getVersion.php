<?php
header('Content-Type: application/json');
$v = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/VERSION");
echo json_encode(array("version" => $v));

?>