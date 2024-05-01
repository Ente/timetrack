<?php
header('Content-Type: application/json');
$v = file_get_contents("../VERSION");
echo json_encode(array("version" => $v));

?>