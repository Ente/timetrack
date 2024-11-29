<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/vendor/autoload.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/class/plugins/loader.plugins.arbeit.inc.php";
require_once dirname(__DIR__, 1) . "/src/Main.php";
require_once dirname(__DIR__, 1) . "/src/Code.php";
require_once dirname(__DIR__, 1) . "/src/Setup.php";

use Arbeitszeit\Arbeitszeit;
use CodeClock\codeclock;
use CodeClock\Code;

$arbeit = new Arbeitszeit;
$main = new codeclock;
$code = new Code;
?>
<div id="plugin-codeclock">
    <h2>CodeClock Plugin</h2>
    <p>Please view your PIN from below. You can then login from <a href="<?php echo "http://" . $arbeit->get_app_ini()["general"]["base_url"] . "/api/v1/toil/code" ?>">here</a></p>
    <p>After login you can clock in or out.</p>

    <br>
    <p>PIN: <span id="pin"><?php echo $code->getUserPIN($_SESSION["username"]) ?></span></p>
</div>