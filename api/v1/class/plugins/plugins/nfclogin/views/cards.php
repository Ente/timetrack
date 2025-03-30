<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/vendor/autoload.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/class/plugins/loader.plugins.arbeit.inc.php";
require_once dirname(__DIR__, 1) . "/src/Main.php";

use Arbeitszeit\Arbeitszeit;
use NFClogin\NFClogin;

$arbeit = new Arbeitszeit;
$main = new NFClogin;
?>
<div id="plugin-nfclogin">
    <h2>NFCLogin Plugin</h2>
    <p>Please reload the page while holding the NFC card to read it.</p>
    <button onclick="startNFC()">Karte scannen</button>

<div id="nfc-modal" style="display:none; position:fixed; inset:0; background:#0009; color:white; padding:2em; text-align:center;">
    <p>Karte bitte an das Lesegerät halten…</p>
    <p id="nfc-status">Warte auf Karte…</p>
</div>

<script>
function startNFC() {
    const modal = document.getElementById('nfc-modal');
    const status = document.getElementById('nfc-status');
    modal.style.display = 'block';
    status.textContent = "Warte auf Karte…";

    fetch('/api/v1/toil/readNfc')
        .then(res => res.json())
        .then(data => {
            if (data.uid) {
                status.textContent = "Karte erkannt: " + data.uid;
                setTimeout(() => location.reload(), 1000);
            } else {
                status.textContent = "Keine Karte erkannt.";
                setTimeout(() => modal.style.display = 'none', 2000);
            }
        })
        .catch(err => {
            status.textContent = "Fehler beim Lesen.";
            console.error(err);
            setTimeout(() => modal.style.display = 'none', 2000);
        });
}
</script>

    <br>
    <p>
    <?php
    try {
        $nfcService = $main;
        $cardData = $nfcService->readCard();

        if ($cardData) {
            echo "<strong>Output/UUID</strong><br><pre>";
            echo htmlspecialchars(json_encode($cardData, JSON_PRETTY_PRINT));
            echo "</pre>";
        } else {
            echo "No card detected.";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
    ?>
    </p>
</div>
