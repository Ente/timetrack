<?php
session_start();
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";

use Arbeitszeit\Arbeitszeit;

$arbeit = new Arbeitszeit;
$ini = $arbeit->get_app_ini();

$arbeit->auth()->login_validation();

$projectId = $_GET["project"] ?? null;
$language = $arbeit->i18n()->loadLanguage(null, "projects/createItem");
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

<main style="max-width: 600px; margin: 0 auto; padding: 2rem;">
    <h1><?= $language["title"]; ?></h1>
    <form action="/suite/actions/projects/createItem.php" method="POST">
        <input type="hidden" name="project_id" value="<?= htmlspecialchars($projectId); ?>">

        <label><?= $language["title"]; ?>:</label><br>
        <input type="text" name="title" required>
        <br><br>
        <label><?= $language["description"]; ?>:</label><br>
        <textarea name="description" placeholder="<?= $language["description"]; ?>"></textarea>
        <br><br>
        <label><?= $language["assignee"]; ?>:</label><br>
        <input type="text" name="assignee">
        <br><br>
        <button type="submit" class="v8-button"><?= $language["btn_save"]; ?></button><br>
        <a href="view.php?id=<?= $projectId; ?>" class="v8-button secondary"><?= $language["btn_cancel"]; ?></a>
    </form>
</main>
</body>
</html>
