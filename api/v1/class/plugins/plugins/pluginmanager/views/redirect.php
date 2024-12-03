<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/class/plugins/plugins/pluginmanager/src/Main.php";
use pluginmanager\pluginmanager;
$main = new pluginmanager();
header("Location: /api/v1/toil/pluginmanager");
?>