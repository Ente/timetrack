<?php
session_start();
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";

use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Projects;

$arbeit = new Arbeitszeit;
$projects = new Projects;

$ini = $arbeit->get_app_ini();
$base_url = $ini["general"]["base_url"];

$language = $arbeit->i18n()->loadLanguage(null, "projects/overview");

$arbeit->auth()->login_validation();

$user = $arbeit->benutzer()->get_current_user();

$userProjects = $arbeit->projects()->getCurrentUserProjects();
$userItems = $arbeit->projects()->getUserProjectItems($userProjects[1]["id"], $user["id"]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $language["title"]; ?> | <?= $ini["general"]["app_name"]; ?></title>
    <link rel="stylesheet" href="/assets/css/v8.css?v=1">
</head>
<body>
    <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/gui/standard_nav.php"; ?>

    <main style="max-width: 1080px; margin: 0 auto; padding: 2rem;">
        <h1><?= $language["title"]; ?></h1>

        <div class="card v8-bordered" style="margin-bottom: 2rem;">
            <p><?= $language["intro"]; ?></p>
        </div>

        <div class="card v8-bordered" style="margin-bottom: 2rem;">
            <h2><?= $language["your_projects"]; ?></h2>
            <ul>
                <?php foreach($userProjects as $proj): ?>
                    <li>
                        <strong><?= htmlspecialchars($proj["name"]); ?></strong>  
                        (<?= $proj["description"]; ?>)
                        <br>
                        <a href="view.php?id=<?= $proj["id"]; ?>" class="v8-link"><?= $language["link_view_project"]; ?></a>
                    </li>
                <?php endforeach; ?>

                <?php if(empty($userProjects)): ?>
                    <li><?= $language["no_projects"]; ?></li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="card v8-bordered">
            <h2><?= $language["your_items"]; ?></h2>
            <div class="table-wrapper">
                <table class="v8-table">
                    <thead>
                        <tr>
                            <th><?= $language["th_item_title"]; ?></th>
                            <th><?= $language["id"]; ?></th>
                            <th><?= $language["th_project"]; ?></th>
                            <th><?= $language["th_status"]; ?></th>
                            <th><?= $language["th_actions"]; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($userItems as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item["title"]); ?></td>
                                <td><?= $item["id"]; ?></td>
                                <td>
                                    <?= $arbeit->projects()->getProject($item["id"])["name"]; ?>
                                </td>
                                <td><?= $item["status"] ?? "Open"; ?></td>
                                <td>
                                    <a href="item.php?id=<?= $item["itemid"]; ?>" class="v8-button"><?= $language["btn_view_item"]; ?></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if(empty($userItems)): ?>
                            <tr>
                                <td colspan="4" style="text-align:center;">
                                    <?= $language["no_items"]; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
