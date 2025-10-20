<?php
require dirname(__DIR__, 2) . "/api/v1/inc/arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
$arbeit = new Arbeitszeit;
$ini = $arbeit->get_app_ini();
$arbeit->auth()->login_validation();

$locale = locale_accept_from_http($_SERVER["HTTP_ACCEPT_LANGUAGE"]); // Locale retrieved from Header
$loc = $arbeit->i18n()->loadLanguage(null, "users/settings");
$data = $arbeit->benutzer()->get_user($_SESSION["username"]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $loc["title"]; ?> | <?= $ini["general"]["app_name"]; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=JetBrains+Mono&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $arbeit->benutzer()->loadUserTheme(); ?>?v=1">
</head>

<body>
    <div class="animated-bg"></div>

    <?php include dirname(__DIR__, 2) . "/assets/gui/standard_nav.php"; ?>

    <main style="max-width: 720px; margin: 0 auto; padding: 2rem;">
        <h2><?= $loc["title"]; ?></h2>

        <div class="card v8-bordered">
            <p><strong><?= $loc["label_name"]; ?>:</strong> <?= htmlspecialchars($data["name"]); ?></p>
            <p><strong><?= $loc["label_id"]; ?>:</strong> <?= htmlspecialchars($data["id"]); ?></p>
            <p><strong><?= $loc["label_email"]; ?>:</strong> <?= htmlspecialchars($data["email"]); ?></p>
            <p><strong><?= $loc["label_username"]; ?>:</strong> <?= htmlspecialchars($data["username"]); ?></p>
            <p><strong><?= $loc["label_provider"]; ?>:</strong> <?= htmlspecialchars($_SESSION["provider"]); ?></p>

            <hr style="margin: 1.5rem 0; border-color: #2a2a2a;">

            <p><?= $loc["note1"]; ?></p>
            <p>
                <?= $loc["note2_a"]; ?>
                <a href="http://<?= $ini["general"]["base_url"]; ?>/suite/users/changes.php"><?= $loc["note2_b"]; ?></a>
                <?= $loc["note2_c"]; ?>
            </p>

            <?php if ($arbeit->benutzer()->is_admin($arbeit->benutzer()->get_user($_SESSION["username"]))): ?>
                <p style="font-size: 0.85rem; color: red; font-family: var(--font-mono);">
                    ⚠️ YOU ARE USING AN ADMIN ACCOUNT!
                </p>
            <?php endif; ?>

            <p style="margin-top: 1rem;"><strong>Status – Easymode:</strong> <?= $arbeit->get_easymode_status($_SESSION["username"]); ?></p>

            <form action="/suite/actions/users/toggle_easymode.php" method="POST" style="margin-top: 1rem;">
                <button type="submit"><?= $loc["easymode_toggle"]; ?></button>
            </form>

            <hr>
            
            <form action="/suite/actions/users/change_theme.php" method="POST">
                <h2>Select theme</h2>
                <label for="theme">Select a theme:</label>
                <select name="theme" id="theme" onchange="this.form.submit()">
                    <?php 
                    if($arbeit->benutzer()->checkThemeForce()){
                        $noTheme =  "<p>You cannot select a theme, since your administrator doesn't allow this feature!</p>";
                    } else {
                        unset($noTheme);
                        $arbeit->benutzer()->computeUserThemes();
                    }
                    
                    
                    ?>
                </select>
                <?= $noTheme; ?>
            </form>
        </div>

        <h2 style="margin-top: 3rem;"><?= $loc["support"]; ?></h2>
        <div class="v8-bordered" style="margin-top: 1rem;">
            <p><?= $loc["support_note"]; ?>:
                <a href="mailto:<?= $ini["general"]["support_email"]; ?>"><?= $ini["general"]["support_email"]; ?></a>
            </p>
            <p>
                <a href="https://tracking.isx.openducks.org/articles/TT-A-5/Roadmap-TimeTrack" target="_blank"><?= $loc["roadmap_note"]; ?></a> |
                <a href="https://github.com/ente/timetrack/issues" target="_blank"><?= $loc["github_note"]; ?></a>
            </p>
            <p>TimeTrack Version: <?= $arbeit->getTimeTrackVersion(); ?> | API Version: <?= $arbeit->getToilVersion(); ?></p>
        </div>

        <?php if ($arbeit->benutzer()->is_admin($arbeit->benutzer()->get_user($_SESSION["username"]))): ?>
            <div style="margin-top: 3rem;">
                <?php require_once "../admin/users/settings.php"; ?>
            </div>
        <?php endif; ?>
    </main>

    <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/gui/standard_footer.php"; ?>
</body>
</html>

