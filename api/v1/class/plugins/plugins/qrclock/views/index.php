<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/vendor/autoload.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/class/plugins/loader.plugins.arbeit.inc.php";
require_once dirname(__DIR__, 1) . "/src/Main.php";

use Arbeitszeit\Arbeitszeit;
use qrclock\qrclock;

$arbeit = new Arbeitszeit;
$main = new qrclock;

if (isset($_GET["action"])) {
    try{
        $payload = json_decode(base64_decode($_GET["payload"]), true);
    } catch (\Exception $e){
        return "An error occured while decoding payload.";
    }
    if ($_GET["action"] === "clockin") {
        if ($main->validateDynamicToken($payload["token"], "1234", $payload["id"])) {
            if ($arbeit->add_easymode_worktime($payload["username"])) {
                echo "<h2 style='color:green'>Successfully clocked in.</h2>";
            } else {
                echo "<h2 style='color:red'>An error occured while clocking in.</h2>";
            }
        } else {
            echo "<h2 style='color:red'>Invalid token.</h2>";
        }
    } elseif ($_GET["action"] === "clockout") { {
            if ($main->validateDynamicToken($payload["token"], "1234", $payload["id"])) {
                if ($arbeit->end_easymode_worktime($payload["username"], $arbeit->check_easymode_worktime_finished($payload["username"]))) {
                    echo "<h2 style='color:green'>Successfully clocked out.</h2>";
                } else {
                    echo "<h2 style='color:green'>An error occured while clocking in.</h2>";
                }
            } else {
                echo "<h2 style='color:red'>Invalid token.</h2>";
            }
        }
    }
}

?>
<div id="plugin-qrcode">
    <h1>QR-Clock Plugin</h1>
        <p>Please use the QRcode below to clockin. You can save this image for later use (usually done by right clicking on the QRcode and then saving onto device.)</p>
        <p>Once you have clocked in either by easy mode or by the QR code a new qr code will be generated to clock out. You can also save this one for later use.</p>
        <p>Username: <?php echo $_SESSION["username"]; ?></p>
    <br>
        <p>Status: <?php echo $main->getStatus(); ?></p><br>
        <p>QR-CODE: <br><img src="<?php echo $main->generateQRCodeContents($_SESSION["username"]) ?>" alt="QR-Code content"></p>
</div>