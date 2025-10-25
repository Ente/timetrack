<?php
session_start();
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";

use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Projects;

$arbeit = new Arbeitszeit;
$projects = new Projects;

$ini = $arbeit->get_app_ini();
$base_url = $ini["general"]["base_url"];

$language = $arbeit->i18n()->loadLanguage(null, "projects/edit", "admin");

$arbeit->auth()->login_validation();
if(!$arbeit->benutzer()->is_admin($arbeit->benutzer()->get_current_user())){
    header("Location: http://{$base_url}/suite/?" . $arbeit->statusMessages()->URIBuilder("noperms"));
    exit;
}

$projectId = $_GET["id"] ?? null;

$project = $arbeit->projects()->getProject($projectId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $language["title"]; ?> | <?= $ini["general"]["app_name"]; ?></title>
    <link rel="stylesheet" href="<?= $arbeit->benutzer()->loadUserTheme(); ?>?v=1">
</head>
<body>
    <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/gui/standard_nav.php"; ?>

    <main style="max-width: 1080px; margin: 0 auto; padding: 2rem;">
        <h1><?= $language["title"]; ?>: <?= htmlspecialchars($project["name"] ?? ""); ?></h1>

        <div class="card v8-bordered">
            <form action="/suite/admin/actions/projects/edit.php" method="POST">
                <!-- Hidden ID -->
                <input type="hidden" name="id" value="<?= $arbeit->i18n()->sanitizeOutput($project["id"] ?? $projectId); ?>">

                <label><?= $language["label_name"]; ?>:</label><br>
                <input type="text" name="name" value="<?= htmlspecialchars($project["name"] ?? ""); ?>" required>
                <br><br>
                <label><?= $language["label_description"]; ?>:</label><br>
                <textarea name="description"><?= htmlspecialchars($project["description"] ?? ""); ?></textarea>
                <br><br>
                <label><?= $language["label_deadline"]; ?>:</label><br>
                <input type="date" name="deadline" value="<?= $arbeit->i18n()->sanitizeOutput($project["deadline"] ?? ""); ?>">
                <br><br>
                <label><?= $language["label_owner"]; ?>:</label><br>
                <input type="text" name="owner" value="<?= $arbeit->i18n()->sanitizeOutput($project["owner"] ?? ""); ?>" placeholder="UserID">
                <br>
                <button type="submit" name="submit" class="v8-button"><?= $language["btn_save"]; ?></button><br><br>
                <a href="admin.php" class="v8-button secondary"><?= $language["btn_cancel"]; ?></a>
            </form>
        </div>
    </main>
    <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/gui/standard_footer.php"; ?>
</body>
</html>
