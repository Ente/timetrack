<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/vendor/autoload.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/class/plugins/loader.plugins.arbeit.inc.php";
require_once dirname(__DIR__, 1) . "/src/Main.php";

use Arbeitszeit\Arbeitszeit;
use NFClogin\NFClogin;

$arbeit = new Arbeitszeit;
$main = new NFClogin;
$arbeit->auth()->login_validation();
$arbeit->blockIfNotAdmin();   
?>

<div id="plugin-nfclogin">
    <h2>NFCLogin Plugin</h2>

    <p>Functions:</p>
    <button class="button" onclick="startNFC()">Read card</button>
    <br><br>
    <form onsubmit="return writeNFC();">
        <label>Write username to card:</label> / <a href="/suite/admin/users/edit.php">Open user list</a><br><br>
        <input class="input" type="text" id="nfc-username" placeholder="username" required>
        <button class="button" type="submit">Write</button>
    </form>
    <br><br>
    <button class="button" onclick="readBlock4()">Read NFC card (user block)</button>
    <hr width="75%">
    <div id="nfc-modal" style="display:none; position:fixed; inset:0; background:#0009; color:white; padding:2em; text-align:center;">
        <p>Please hold your card near the device</p>
        <p id="nfc-status">Waiting for card...</p>
    </div>

    <br>
    <p>
    <?php
    try {
        $cardData = $main->readCard();
        if ($cardData) {
            echo "<strong>Initial check (technical data may be provided):</strong><br><pre>";
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

<script>
function showModal(text) {
    const modal = document.getElementById('nfc-modal');
    const status = document.getElementById('nfc-status');
    status.textContent = text;
    modal.style.display = 'block';
}

function hideModal(delay = 2000) {
    setTimeout(() => {
        document.getElementById('nfc-modal').style.display = 'none';
    }, delay);
}

function startNFC() {
    showModal("Waiting for card...");
    fetch('/api/v1/toil/readNfc')
        .then(res => res.json())
        .then(data => {
            if (data.uid) {
                document.getElementById('nfc-status').textContent = "Card detected: " + data.uid;
                setTimeout(() => location.reload(), 1000);
            } else {
                document.getElementById('nfc-status').textContent = "No card detected.";
                hideModal();
            }
        })
        .catch(err => {
            console.error(err);
            document.getElementById('nfc-status').textContent = "Error while reading card.";
            hideModal();
        });
}

function writeNFC() {
    const username = document.getElementById('nfc-username').value;
    if (!username) return false;

    showModal("Writing to card...");
    fetch('/api/v1/toil/writeNfc', {
        method: "POST",
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ username: username })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success || data.uid) {
            document.getElementById('nfc-status').textContent = "Successfully written to card.";
            setTimeout(() => location.reload(), 10000);
        } else {
            document.getElementById('nfc-status').textContent = "Error while writing to card.";
            hideModal();
        }
    })
    .catch(err => {
        console.error(err);
        document.getElementById('nfc-status').textContent = "Error while writing to card.";
        hideModal();
    });

    return false;
}

function readBlock4() {
    showModal("Reading user block...");
    fetch('/api/v1/toil/readBlock4')
        .then(res => res.json())
        .then(data => {
            if (data.block) {
                document.getElementById('nfc-status').textContent = "Block 4: " + data.block;
            } else {
                document.getElementById('nfc-status').textContent = "Error while reading block 4.";
            }
            hideModal(3000);
        })
        .catch(err => {
            console.error(err);
            document.getElementById('nfc-status').textContent = "Error while reading.";
            hideModal();
        });
}
</script>
