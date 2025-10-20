<?php
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
use Arbeitszeit\Arbeitszeit;
session_start();
$arbeit = new Arbeitszeit;
$arbeit->autodelete()->autodelete_obsolete_notifications_entries();
$ini = $arbeit->get_app_ini();
$base_url = $ini["general"]["base_url"];
$arbeit->auth()->login_validation();
$language = $arbeit->i18n()->loadLanguage(NULL, "index");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $language["title"] ?> | <?= $ini["general"]["app_name"]; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Fonts & CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=JetBrains+Mono&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $arbeit->benutzer()->loadUserTheme(); ?>?v=1">

    <!-- JS Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js" defer></script>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" defer></script>
</head>

<body>
    <div class="animated-bg"></div>

    <!-- Notification Bar -->
    <?php $arbeit->notifications()->get_notifications_html(); ?>

    <!-- Navigation -->
    <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/gui/standard_nav.php"; ?> 

    <main style="max-width: 960px; margin: 0 auto; padding: 2rem;">
        <h1 style="font-size: 2rem; margin-bottom: 1rem;"><?= $language["title"]; ?> | <?= $ini["general"]["app_name"]; ?></h1>

        <div class="card">
            <h2 style="margin-bottom: 1rem;"><?= $language["action_addshift"]; ?></h2>
            <?= $arbeit->mode()->check($_SESSION["username"]); ?>
        </div>

        <p style="margin-top: 2rem; opacity: 0.8;">
            <?= $language["note_footer"]; ?>
            <a href="mailto:<?= $ini["general"]["support_email"]; ?>"><?= $ini["general"]["support_email"]; ?></a>
        </p>
    </main>

    <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/gui/standard_footer.php"; ?> 
</body>
</html>

