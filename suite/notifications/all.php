<?php
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
$arbeit = new Arbeitszeit;
$ini = $arbeit->get_app_ini();
$base_url = $ini["general"]["base_url"];
$loc = $arbeit->i18n()->loadLanguage(null, "notifications/all");
$arbeit->auth()->login_validation();
if(!$arbeit->benutzer()->is_admin($arbeit->benutzer()->get_user($_SESSION["username"]))){
    header("Location: http://{$base_url}/suite/?" . $arbeit->statusMessages()->URIBuilder("noperms"));
}
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

    <?php require $_SERVER["DOCUMENT_ROOT"] . "/assets/gui/standard_nav.php"; ?>

    <main style="max-width: 960px; margin: 0 auto; padding: 2rem;">
        <h1><?= $loc["title"]; ?></h1>

        <!-- Hinweis -->
        <div class="card v8-bordered" style="margin-bottom: 2rem;">
            <h2><?= $loc["note1"]; ?></h2>
            <p><?= $loc["p1"]; ?></p>
            <p><?= $loc["p2"]; ?></p>
            <p><span style="color: red;"><?= $loc["note2"]; ?></span></p>
        </div>

        <!-- Formular -->
        <div class="card v8-bordered" style="margin-bottom: 2rem;">
            <form method="POST" action="../admin/actions/notifications/add.php">
                <label><?= $loc["label_date"]; ?></label>
                <input type="date" value="<?= date("Y-m-d"); ?>" name="datum" required>

                <label style="margin-top: 1rem;"><?= $loc["label_time"]; ?></label>
                <input type="time" value="<?= date("H:i"); ?>" name="uhrzeit" required>

                <label style="margin-top: 1rem;"><?= $loc["label_location"]; ?></label>
                <input type="text" name="ort" placeholder="Ort">
                <br>
                <label style="margin-top: 1rem;"><?= $loc["label_note"]; ?></label>
                <textarea name="notiz" placeholder="<?= $loc["pl_note"]; ?>"></textarea>

                <button type="submit" style="margin-top: 1rem;"><?= $loc["button_send"]; ?></button>
            </form>
        </div>

        <!-- Einträge -->
        <div class="card v8-bordered">
            <h2><?= $loc["a_entries"]; ?>:</h2>

            <div class="table-wrapper">
                <table class="v8-table">
                    <thead>
                        <tr>
                            <th>- - - -</th>
                            <th><?= $loc["label_date"]; ?></th>
                            <th><?= $loc["label_time"]; ?></th>
                            <th><?= $loc["label_location"]; ?></th>
                            <th><?= $loc["label_note"]; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?= $arbeit->notifications()->get_notifications_edit_html(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/gui/standard_footer.php"; ?>
</body>
</html>
