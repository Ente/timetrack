<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/class/plugins/loader.plugins.arbeit.inc.php";
require_once dirname(__DIR__, 1) . "/src/Main.php";

use userdetail\Main;

$main = new Main;

$count = $main->get_users();
?>
<p>Amount of users: <?php echo $count; ?></p>