<?php
session_start();
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";

use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Projects;

$arbeit = new Arbeitszeit;
$projects = new Projects;

$ini = $arbeit->get_app_ini();
$base_url = $ini["general"]["base_url"];

$language = $arbeit->i18n()->loadLanguage(null, "projects/view");

$arbeit->auth()->login_validation();

$user = $arbeit->benutzer()->get_current_user();

$projectId = $_GET["id"] ?? null;
$project = $arbeit->projects()->getProject($projectId);

$items = $arbeit->projects()->getProjectItems($projectId);
$members = $arbeit->projects()->getProjectUsers($projectId);
$worktimes = $arbeit->projects()->getUserProjectWorktimes($items) ?? false;
$arbeit->statusMessages()->blockIfNotAdmin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $language["title"]; ?> | <?= $ini["general"]["app_name"]; ?></title>
    <link rel="stylesheet" href="<?= $arbeit->benutzer()->loadUserTheme(); ?>?v=1">
    <style>
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</head>
<body>
    <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/gui/standard_nav.php"; ?>

    <main style="max-width: 1080px; margin: 0 auto; padding: 2rem;">
        <h1><?= htmlspecialchars($project["name"] ?? $language["title"]); ?></h1>
        <p><?= $language["description"] ?>: <?= htmlspecialchars($project["description"] ?? $language["no_description"]); ?></p>
        <p><strong><?= $language["deadline"]; ?>:</strong> <?= $arbeit->i18n()->sanitizeOutput($project["deadline"] ?? "-"); ?></p>

        <!-- Tabs -->
        <div class="tabs">
            <button onclick="showTab('items')"><?= $language["tab_items"]; ?></button>
            <button onclick="showTab('members')"><?= $language["tab_members"]; ?></button>
            <button onclick="showTab('worktimes')"><?= $language["tab_worktimes"]; ?></button>
        </div>

        <div id="items" class="tab-content">
            <div class="section-header">
                <h2><?= $language["items"]; ?></h2>
                <a href="/suite/projects/createItem.php?project=<?= $projectId; ?>" class="v8-button">+</a>
            </div>
            <table class="v8-table">
                <thead>
                    <tr>
                        <th><?= $language["th_item"]; ?></th>
                        <th><?= $language["th_assignee"]; ?></th>
                        <th><?= $language["th_status"]; ?></th>
                        <th><?= $language["th_actions"]; ?></th>
                        <th><?= $language["id"]; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($items as $i): ?>
                        <tr>
                            <td><?= htmlspecialchars($i["title"]); ?></td>
                            <td><?= $arbeit->benutzer()->get_user_from_id($i["assignee"])["name"] ?? "-"; ?></td>
                            <td><?= $arbeit->i18n()->sanitizeOutput($i["status"] ?? "Open"); ?></td>
                            <td><a href="item.php?id=<?= $arbeit->i18n()->sanitizeOutput($i["id"]); ?>" class="v8-button"><?= $language["btn_view"]; ?></a></td>
                            <td><?= $arbeit->i18n()->sanitizeOutput($i["itemid"] ?? ""); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if(empty($items)): ?>
                        <tr><td colspan="4"><?= $language["no_items"]; ?></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div id="members" class="tab-content" style="display:none;">
            <div class="section-header">
                <h2><?= $language["members"]; ?></h2>
                <?php $isAdmin = $arbeit->benutzer()->get_current_user()["isAdmin"]; ?>
                <?php if($isAdmin): ?>
                    <a href="/suite/admin/projects/addUser.php?project=<?= $arbeit->i18n()->sanitizeOutput($projectId); ?>" class="v8-button">+</a>
                <?php endif; ?>
            </div>
            <ul>
                <?php foreach($members as $m): ?>
                    <li><?= $arbeit->i18n()->sanitizeOutput($m["name"]); ?> (<?= $arbeit->i18n()->sanitizeOutput($m["role"]); ?>) - UserID: <?= $arbeit->i18n()->sanitizeOutput($m["userid"]); ?></li>
                <?php endforeach; ?>
                <?php if(empty($members)): ?>
                    <li><?= $language["no_members"]; ?></li>
                <?php endif; ?>
            </ul>
        </div>

        <div id="worktimes" class="tab-content" style="display:none;">
            <div class="section-header">
                <h2><?= $language["worktimes"]; ?></h2>
                <a href="/suite/projects/mapWorktimeToItem.php?project=<?= $arbeit->i18n()->sanitizeOutput($projectId); ?>" class="v8-button">+</a>
            </div>
            <table class="v8-table">
                <thead>
                    <tr>
                        <th><?= $language["th_user"]; ?></th>
                        <th><?= $language["th_item"]; ?></th>
                        <th><?= $language["th_hours"]; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($worktimes as $w): ?>
                        <tr>
                            <td><?= $arbeit->benutzer()->get_user_from_id($w["user"])["name"] ?? "-"; ?></td>
                            <td><?= $arbeit->i18n()->sanitizeOutput($w["itemid"] ?? ""); ?></td>
                            <td><?= $arbeit->i18n()->sanitizeOutput($w["hours"] ?? "-"); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if(empty($worktimes)): ?>
                        <tr><td colspan="3"><?= $language["no_worktimes"]; ?></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        function showTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(el => el.style.display = "none");
            document.getElementById(tabId).style.display = "block";
        }
    </script>
</body>
</html>
