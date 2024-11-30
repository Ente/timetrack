<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/v1/inc/arbeit.inc.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/v1/class/plugins/plugins/exportmanager/src/Main.php';

use ExportManager\exportmanager;

$exportmanager = new exportmanager();

if($exportmanager->setup()){
    header("Location: /api/v1/toil/exportmanager");
}
header("Location: /api/v1/toil/exportmanager");