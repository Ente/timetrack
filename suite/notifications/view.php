<?php
require dirname(__DIR__, 2) . "/api/v1/inc/arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
$arbeit = new Arbeitszeit;
$arbeit->auth()->login_validation();
$id = $_GET["id"];
$data = $arbeit->notifications()->get_notifications_entry($id);

if(isset($data["error"])){
    header("Location: /suite/?" . $arbeit->statusMessages()->URIBuilder("notification_not_found"));
    exit;
}

$loc = $arbeit->i18n()->loadLanguage(null, "notifications/view");
$iid = htmlspecialchars($id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $loc["title"]; ?> | <?= $arbeit->get_app_ini()["general"]["app_name"]; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=JetBrains+Mono&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $arbeit->benutzer()->loadUserTheme(); ?>?v=1">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js" defer></script>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" defer></script>
</head>

<body>
    <div class="animated-bg"></div>

    <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/gui/standard_nav.php"; ?>

    <main style="max-width: 720px; margin: 0 auto; padding: 2rem;">
        <h2><?= $loc["h2"]; ?> <?= @strftime("%d.%m.%Y", strtotime($data["datum"])); ?></h2>

        <div class="card v8-bordered">
            <p><strong><?= $loc["label_location"]; ?>:</strong> <?= htmlspecialchars($data["ort"]); ?></p>
            <p><strong><?= $loc["label_date"]; ?>:</strong> <?= @strftime("%d.%m.%Y", strtotime($data["datum"])); ?></p>
            <p><strong><?= $loc["label_time"]; ?>:</strong> <?= htmlspecialchars($data["uhrzeit"]); ?></p>
            <p><strong><?= $loc["label_note"]; ?>:</strong><br><?= nl2br(htmlspecialchars($data["notiz"])); ?></p>
        </div>

        <?php
        if ($arbeit->benutzer()->is_admin($arbeit->benutzer()->get_user($_SESSION["username"]))) {
            echo <<<HTML
            <div style="margin-top: 2rem;">
                <a href="../admin/actions/notifications/delete.php?id={$iid}" style="color: #e74c3c; font-weight: bold;">
                    🗑️ {$loc["a_delete"]}
                </a>
                <p style="color: red; font-size: 0.9rem; margin-top: 0.25rem;">{$loc["a_delete_note"]}</p>
            </div>
            HTML;
        }
        ?>
    </main>

    <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/gui/standard_footer.php"; ?>
</body>
</html>
