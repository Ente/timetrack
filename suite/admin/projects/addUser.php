<?php
session_start();
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";

use Arbeitszeit\Arbeitszeit;

$arbeit = new Arbeitszeit;
$ini = $arbeit->get_app_ini();

$arbeit->auth()->login_validation();
if(!$arbeit->benutzer()->is_admin($arbeit->benutzer()->get_current_user())){
    die("No permissions");
}

$projectId = $_GET["project"] ?? null;
$language = $arbeit->i18n()->loadLanguage(null, "projects/addUser");
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
    <h1><?= $language["title2"] ?></h1>
    <form action="/suite/admin/actions/projects/userEdit.php" method="POST">
        <input type="hidden" name="project" value="<?= htmlspecialchars($projectId); ?>">

        <label><?= $language["label_userid"]; ?>:</label><br>
        <input type="text" name="projectId" value="<?= $_GET["project"]; ?>" hidden>
        <input type="text" name="userId" required>
        <br><br>
        <label><?= $language["label_role"]; ?>:</label><br>
        <input type="text" name="role" placeholder="Member / Admin">
        <br><br>
        <label><?= $language["label_permissions"]; ?>:</label><br>
        <input type="number" name="permissions" value="0" min="0" max="2" ><br>
        <span class="tooltip-text"><?= $language["tooltip_permissions"]; ?></span>
        <br><br>
        <button type="submit" class="v8-button"><?= $language["btn_add"]; ?></button><br>
        <a href="/suite/projects/view.php?id=<?= $projectId; ?>" class="v8-button secondary"><?= $language["btn_cancel"]; ?></a>
    </form>
</main>
</body>
</html>
