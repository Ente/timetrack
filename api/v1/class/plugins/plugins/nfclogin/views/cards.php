<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/vendor/autoload.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/class/plugins/loader.plugins.arbeit.inc.php";
require_once dirname(__DIR__, 1) . "/src/Main.php";

use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Exceptions;
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
        Exceptions::error_rep("An error occured while reading the card: " . $e->getMessage());
    }
    ?>
    </p><hr width="75%">
    <div id="allcards" style="text-align: center;">
        <h2>All Cards</h2>
        <?php echo $main->allCardAssignmentsHtml(); ?>
    </div>
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
                document.getElementById('nfc-status').textContent = data.error || "No card detected.";
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
    fetch('/api/v1/toil/writeNfc?username=' + username, {
        method: "GET"
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('nfc-status').textContent = "Successfully block: " + (data.block || username);
            setTimeout(() => location.reload(), 2000);
        } else {
            document.getElementById('nfc-status').textContent = data.error || "Write failed.";
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
            if (data.value) {
                document.getElementById('nfc-status').textContent = "Block 4: " + data.value;
                if (data.username) {
                    document.getElementById('nfc-status').innerHTML = "<strong style='color:green;'>USER VERIFICATION OK</strong> " + data.username;
                }
            } else {
                document.getElementById('nfc-status').textContent = data.error || "Read failed.";
            }
            hideModal(3000);
        })
        .catch(err => {
            console.error(err);
            document.getElementById('nfc-status').textContent = "Error while reading block.";
            hideModal();
        });
}

</script>
