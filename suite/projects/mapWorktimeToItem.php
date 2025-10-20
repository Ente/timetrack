<?php
session_start();
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";

use Arbeitszeit\Arbeitszeit;

$arbeit = new Arbeitszeit;
$ini = $arbeit->get_app_ini();

$arbeit->auth()->login_validation();

$projectId = $_GET["project"] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Map Worktime | <?= $ini["general"]["app_name"]; ?></title>
    <link rel="stylesheet" href="<?= $arbeit->benutzer()->loadUserTheme(); ?>?v=1">
</head>
<body>
<?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/gui/standard_nav.php"; ?>

<main style="max-width: 600px; margin: 0 auto; padding: 2rem;">
    <h1>Map Worktime to Item</h1>
    <form action="/suite/actions/projects/mapWorktimeToItem.php" method="POST">
        <input type="hidden" name="project" value="<?= htmlspecialchars($projectId); ?>">

        <label>Worktime ID:</label><br>
        <input type="text" name="worktime_id" required>
        <br><br>
        <label>Item ID:</label><br>
        <input type="text" name="item_id" required>
        <br><br>
        <label>User ID (optional):</label><br>
        <input type="text" name="user_id">
        <br><br>
        <button type="submit" class="v8-button">Map</button><br>
        <a href="view.php?id=<?= $projectId; ?>" class="v8-button secondary">Cancel</a>
    </form>
</main>
</body>
</html>
