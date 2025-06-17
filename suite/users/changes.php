<?php
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
$arbeit = new Arbeitszeit;
$base_url = $ini = Arbeitszeit::get_app_ini();
$arbeit->auth()->login_validation();
$locale = locale_accept_from_http($_SERVER["HTTP_ACCEPT_LANGUAGE"]); // Locale retrieved from Header
$loc = $arbeit->i18n()->loadLanguage($locale, "users/changes");
$data = $arbeit->benutzer()->get_user($_SESSION["username"]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $loc["title"]; ?> | <?= $ini["general"]["app_name"]; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=JetBrains+Mono&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/v8.css?v=1">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js" defer></script>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" defer></script>
</head>

<body>
    <div class="animated-bg"></div>

    <?php $arbeit->notifications()->get_notifications_html(); ?>
    <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/gui/standard_nav.php"; ?>

    <main style="max-width: 960px; margin: 0 auto; padding: 2rem;">
        <h1><?= $loc["title"]; ?> | <?= $ini["general"]["app_name"]; ?></h1>

        <div class="card v8-bordered" style="max-height: 80vh; overflow-y: auto;">
            <h2 style="margin-bottom: 1rem;"><?= $loc["title"]; ?></h2>
            <?php
                require $_SERVER["DOCUMENT_ROOT"] . "/vendor/autoload.php";
                $parsedown = new Parsedown();
                echo $parsedown->text(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/CHANGELOG.md"));
            ?>
        </div>
    </main>

    <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/gui/standard_footer.php"; ?>
</body>
</html>
