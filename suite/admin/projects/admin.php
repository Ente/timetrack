<?php
session_start();
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";

use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Projects;

$arbeit = new Arbeitszeit;
$projects = new Projects;

$ini = $arbeit->get_app_ini();
$base_url = $ini["general"]["base_url"];

$language = $arbeit->i18n()->loadLanguage(null, "projects/admin", "admin");

$arbeit->auth()->login_validation();
if (!$arbeit->benutzer()->is_admin($arbeit->benutzer()->get_current_user())) {
    header("Location: http://{$base_url}/suite/?" . $arbeit->statusMessages()->URIBuilder("noperms"));
    exit;
}

$allProjects = $arbeit->projects()->getCurrentUserProjects();
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
        <h1><?= $language["title"]; ?></h1>

        <div class="card v8-bordered" style="margin-bottom: 2rem;">
            <h2><?= $language["existing_projects"]; ?></h2>
            <table class="v8-table">
                <thead>
                    <tr>
                        <th><?= $language["th_id"]; ?></th>
                        <th><?= $language["th_name"]; ?></th>
                        <th><?= $language["th_deadline"]; ?></th>
                        <th><?= $language["th_owner"]; ?></th>
                        <th><?= $language["th_actions"]; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($allProjects as $proj): ?>
                        <tr>
                            <td><?= $proj["id"]; ?></td>
                            <td><?= htmlspecialchars($proj["name"]); ?></td>
                            <td><?= $proj["deadline"]; ?></td>
                            <td><?= $arbeit->benutzer()->get_name_from_id($proj["owner"]); ?></td>
                            <td>
                                <a href="edit.php?id=<?= $proj["id"]; ?>"
                                    class="v8-button"><?= $language["btn_edit"]; ?></a>
                                <a href="delete.php?id=<?= $proj["id"]; ?>" onclick="return confirmDelete(event, <?= $proj["id"]; ?>)"
                                    class="v8-button danger"><?= $language["btn_delete"];  ?></a>

                                <script>
                                    function confirmDelete(e, projectId) {
                                        e.preventDefault(); 
                                        if (confirm("<?php echo $language["delete_confirm"] ?>")) {
                                            
                                            window.location.href = "/suite/admin/actions/projects/delete.php?id=" + projectId;
                                        } else {
                                            
                                            return false;
                                        }
                                    }
                                </script>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($allProjects)): ?>
                        <tr>
                            <td colspan="5" style="text-align:center;">
                                <?= $language["no_projects"]; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Projekt hinzufügen -->
        <div class="card v8-bordered" style="text-align: center;">
            <h2><?= $language["add_project"]; ?></h2>
            <form action="/suite/admin/actions/projects/add.php" method="POST">
                <label><?= $language["label_name"]; ?>:</label>
                <input type="text" name="name" required>
                <br>
                <label><?= $language["label_description"]; ?>:</label><br>
                <textarea name="description"></textarea>
                <br>
                <label><?= $language["label_assoc"]; ?>:</label>
                <input type="text" name="items_assoc">
                <br>
                <label><?= $language["label_owner"]; ?>:</label>
                <input type="text" name="owner" placeholder="UserID oder leer für aktuellen User"
                    value="<?= $arbeit->benutzer()->get_current_user()["id"] ?>">
                <br>
                <button type="submit" name="submit"><?= $language["btn_add"]; ?></button>
            </form>
        </div>
    </main>
</body>

</html>