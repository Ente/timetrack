<?php
session_start();
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";

use Arbeitszeit\Arbeitszeit;

$arbeit = new Arbeitszeit;
$ini = $arbeit->get_app_ini();
$base_url = $ini["general"]["base_url"];

$language = $arbeit->i18n()->loadLanguage(null, "worktime/correction");

$arbeit->auth()->login_validation();

$user = $arbeit->benutzer()->get_current_user();

$worktimeId = $_GET["id"] ?? null;
$worktime = $arbeit->get_worktime_by_id($worktimeId);
if(!$arbeit->check_if_for_review($worktimeId)){
    $arbeit->statusMessages()->redirect("error");
}
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

<main style="max-width: 800px; margin: 0 auto; padding: 2rem;">
    <h1><?= $language["title"]; ?></h1>

    <div class="card v8-bordered" style="margin-bottom: 2rem;">
        <h2><?= $language["h2"]; ?></h2>
        <p><strong><?= $language["start"]; ?>:</strong> <?= $arbeit->i18n()->sanitizeOutput($worktime["schicht_anfang"] ?? "?"); ?></p>
        <p><strong><?= $language["end"]; ?>:</strong> <?= $arbeit->i18n()->sanitizeOutput($worktime["schicht_ende"] ?? "?"); ?></p>
        <p><strong><?= $language["comment"]; ?>:</strong> <?= $arbeit->i18n()->sanitizeOutput($worktime["ort"] ?? "-"); ?></p>
        <p><strong><?= $language["date"]; ?>:</strong> <?= $arbeit->i18n()->sanitizeOutput($worktime["schicht_tag"] ?? "-"); ?></p>
    </div>

    <div class="card v8-bordered" style="text-align: center;">
        <h2><?= $language["suggest_change"]; ?></h2>
        <form action="/suite/actions/worktime/correction.php" method="POST">
            <input type="hidden" name="worktime_id" value="<?= $arbeit->i18n()->sanitizeOutput($worktimeId); ?>">

            <label><?= $language["new_start"]; ?>:</label><br>
            <input type="time" data-date-format="DD.MM.YYYY" name="new_start"><br>

            <label><?= $language["new_end"]; ?>:</label><br>
            <input type="time" data-date-format="DD.MM.YYYY" name="new_end"><br>

            <label><?= $language["new_comment"]; ?>:</label><br>
            <textarea name="new_comment"></textarea><br>

            <label><?= $language["reason"]; ?> <span style="color:red;">*</span>:</label><br>
            <textarea name="reason" required></textarea><br>

            <button type="submit" class="v8-button" style="margin-top:1rem;">
                <?= $language["btn_submit"]; ?>
            </button>
        </form>
    </div>
</main>
    <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/gui/standard_footer.php"; ?> 
</body>
</html>
