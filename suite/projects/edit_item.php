<?php
session_start();
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";

use Arbeitszeit\Arbeitszeit;

$arbeit = new Arbeitszeit;
$ini = $arbeit->get_app_ini();
$language = $arbeit->i18n()->loadLanguage(null, "projects/item");
$arbeit->auth()->login_validation();

$itemId = $_GET["id"] ?? null;
$item = $arbeit->projects()->getItem($itemId);

if (!$item) {
    die("Item not found.");
}

$project = $arbeit->projects()->getProject($item["pid"]);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $language["edit_title"]; ?> | <?= $ini["general"]["app_name"]; ?></title>
    <link rel="stylesheet" href="<?= $arbeit->benutzer()->loadUserTheme(); ?>?v=1">
</head>
<body>
<?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/gui/standard_nav.php"; ?>

<main style="max-width: 1080px; margin: 0 auto; padding: 2rem;">

    <h1><?= $language["edit_title"]; ?> #<?= htmlspecialchars($itemId); ?></h1>
    <p><?= htmlspecialchars($project["name"] ?? ""); ?></p>

    <form action="/suite/actions/projects/edit_item.php" method="POST" class="v8-form card v8-bordered" style="padding:2rem; margin-top:1rem;">
        <input type="hidden" name="id" value="<?= htmlspecialchars($itemId); ?>">

        <label><?= $language["title_label"]; ?></label>
        <input type="text" name="title" value="<?= htmlspecialchars($item["title"]); ?>" required>

        <label><?= $language["description_label"]; ?></label>
        <textarea name="description" rows="5" required><?= htmlspecialchars($item["description"]); ?></textarea>

        <label><?= $language["assignee"]; ?></label>
        <?php  $arbeit->benutzer()->renderUserSelect("assignee", $item["assignee"], $language["no_assignee"]); ?>
        <button class="v8-button" style="margin-top:1.5rem; width:100%;"><?= $language["btn_save"]; ?></button>
    </form>

</main>

<?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/gui/standard_footer.php"; ?>
</body>
</html>
