<?php
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
$username = $_SESSION["username"];
$arbeit = new Arbeitszeit;
$ini = $arbeit->get_app_ini();
$base_url = $ini["general"]["base_url"];
$language = $arbeit->i18n()->loadLanguage(null, "notifications/edit", "admin");

$arbeit->auth()->login_validation();
if(!@$arbeit->benutzer()->is_admin($arbeit->benutzer()->get_user($username))){
    header("Location: http://{$base_url}/suite/?" . $arbeit->statusMessages()->URIBuilder("noperms"));
}
$id = htmlspecialchars($_GET["id"]);
$data = $arbeit->notifications()->get_notifications_entry($id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $language["title"]; ?> | <?= $ini["general"]["app_name"]; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=JetBrains+Mono&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $arbeit->benutzer()->loadUserTheme(); ?>?v=1">
</head>

<body>
    <div class="animated-bg"></div>
    <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/gui/standard_nav.php"; ?>

    <main style="max-width: 720px; margin: 0 auto; padding: 2rem;">
        <h1><?= $language["title"]; ?></h1>

        <div class="card v8-bordered">
            <form action="/suite/admin/actions/notifications/edit.php?id=<?= urlencode($id); ?>" method="POST">
                <label><?= $language["label_date"]; ?></label>
                <input type="date" name="datum" value="<?= htmlspecialchars($data["datum"]); ?>" required>

                <label><?= $language["label_time"]; ?></label>
                <input type="time" name="uhrzeit" value="<?= htmlspecialchars($data["uhrzeit"]); ?>" required>

                <label><?= $language["label_location"]; ?></label>
                <input type="text" name="ort" value="<?= htmlspecialchars($data["ort"]); ?>" placeholder="Ort" required>

                <label><?= $language["label_note"]; ?></label>
                <textarea name="notiz" placeholder="..."><?= htmlspecialchars($data["notiz"]); ?></textarea>

                <button type="submit" name="submit" style="margin-top: 1rem;">
                    <?= $language["submit_text"]; ?>
                </button>
            </form>
        </div>
    </main>

    <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/gui/standard_footer.php"; ?>
</body>
</html>
