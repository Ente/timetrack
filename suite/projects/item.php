<?php
session_start();
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";

use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Projects;

$arbeit = new Arbeitszeit;
$projects = new Projects;

$ini = $arbeit->get_app_ini();
$base_url = $ini["general"]["base_url"];

$language = $arbeit->i18n()->loadLanguage(null, "projects/item");

$arbeit->auth()->login_validation();

$user = $arbeit->benutzer()->get_current_user();

$itemId = $_GET["id"] ?? null;

$item = $arbeit->projects()->getItem($itemId);
$project = $arbeit->projects()->getProject($item["id"]);
$worktimes = $arbeit->projects()->getUserProjectWorktimes($project["id"]);

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
        <h1><?= htmlspecialchars($item["title"] ?? $language["title"]); ?></h1>
        <p><?= htmlspecialchars($item["description"] ?? $language["no_description"]); ?></p>

        <p><strong><?= $language["project"]; ?>:</strong> <?= $project["name"] ?? "-"; ?></p>
        <p><strong><?= $language["assignee"]; ?>:</strong> <?= $arbeit->benutzer()->get_user_from_id($item["assignee"])["name"] ?? "-"; ?></p>
        <p><strong><?= $language["status"]; ?>:</strong> <?= $item["status"] ?? "Open"; ?></p>
        <p><strong><?= $language["id"]; ?></strong>: <?= $item["itemid"]; ?></p>

        <div class="card v8-bordered" style="margin-top:2rem;">
            <h2><?= $language["worktimes"]; ?></h2>
            <table class="v8-table">
                <thead>
                    <tr>
                        <th><?= $language["th_user"]; ?></th>
                        <th><?= $language["th_hours"]; ?></th>
                        <th><?= $language["th_date"]; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($worktimes as $w): ?>
                        <tr>
                            <td><?= $w["user"]; ?></td>
                            <td><?= $w["hours"] ?? "-"; ?></td>
                            <td><?= $w["date"] ?? "-"; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if(empty($worktimes)): ?>
                        <tr><td colspan="3"><?= $language["no_worktimes"]; ?></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="card v8-bordered" style="margin-top:2rem; text-align:center;">
            <a href="edit_item.php?id=<?= $itemId; ?>" class="v8-button"><?= $language["btn_edit"]; ?></a>
            <a href="delete_item.php?id=<?= $itemId; ?>" class="v8-button danger"><?= $language["btn_delete"]; ?></a>
        </div>
    </main>
</body>
</html>
