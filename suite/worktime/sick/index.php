<?php
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
$arbeit = new Arbeitszeit;
$base_url = $ini = $arbeit->get_app_ini();
$loc = $arbeit->i18n()->loadLanguage(null, "sickness");
$arbeit->auth()->login_validation();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $loc["title"]; ?> | <?= $ini["general"]["app_name"]; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=JetBrains+Mono&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/v8.css?v=1">
</head>

<body>
    <div class="animated-bg"></div>

    <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/gui/standard_nav.php"; ?>

    <main style="max-width: 720px; margin: 0 auto; padding: 2rem;">
        <h1><?= $loc["title"]; ?> | <?= $ini["general"]["app_name"]; ?></h1>

        <div class="card v8-bordered">
            <h2><?= $loc["h2"]; ?></h2>

            <form action="/suite/actions/worktime/add_sick.php" method="POST" style="margin-top: 1rem;">
                <p><?= $loc["note1"]; ?></p>
                <input type="date" name="date-start">

                <p style="margin-top: 1rem;"><?= $loc["note2"]; ?>:</p>
                <input type="date" name="date-end">

                <button type="submit" style="margin-top: 1.5rem;">
                    <?= $loc["button_submit"]; ?>
                </button>
            </form>

            <p style="margin-top: 2rem; font-size: 0.95rem; opacity: 0.8;">
                <?= $loc["note3"]; ?>
            </p>
        </div>
    </main>

    <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/gui/standard_footer.php"; ?>
</body>
</html>
