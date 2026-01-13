<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/class/plugins/loader.plugins.arbeit.inc.php";
require_once dirname(__DIR__, 1) . "/src/Main.php";

use Userdetail\Userdetail;
use Arbeitszeit\Arbeitszeit;

$main = new Userdetail;
$arbeit = new Arbeitszeit;

if($arbeit->benutzer()->current_user_is_admin() === true){
    $_SESSION["overWritePerms"] = true;
} else {
    unset($_SESSION["overWritePerms"]);
}

$nav = $main->compute_user_nav();
?>

<?php echo $nav ?>