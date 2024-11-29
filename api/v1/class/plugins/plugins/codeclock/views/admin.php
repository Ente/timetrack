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
use CodeClock\Setup;

$arbeit = new Arbeitszeit;
$main = new codeclock;
$code = new Code;
$setup = new Setup;
$message = "Ready to reset all PINs?";
if(isset($_GET["reset"])) {
    //remove token file
    unlink(dirname(__DIR__, 1) . "/data/token");
    if($setup->done()){
        $message = "PINs have been reset.";
    }
}
?>
<div id="plugin-codeclock">
    <h2>CodeClock Plugin - Reset PINs</h2>
    <p>After clicking on reset all PINs will be regenerated.</p>
    <p>Message: <span><?php echo $message; ?></span></p>
    <a href="?pn=codeclock&p_view=views/admin.php&reset=true" class="button">Reset All PINs</a>
    
</div>