<?php
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
$arbeit = new Arbeitszeit;
$ini = $arbeit->get_app_ini();
$base_url = $ini["general"]["base_url"];
$loc = $arbeit->i18n()->loadLanguage(null, "worktime/all", "admin");
$arbeit->auth()->login_validation();
if(!$arbeit->benutzer()->is_admin($arbeit->benutzer()->get_user($_SESSION["username"]))){
    header("Location: http://{$base_url}/suite/?" . $arbeit->statusMessages()->URIBuilder("noperms"));
}
if(!is_string(@$_POST["jahr"]) || !is_string(@$_POST["monat"])){
    $date_year = date("Y");
    $date_month = date("m");
} else {
    $date_year = $_POST["jahr"];
    $date_month = $_POST["monat"];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $loc["title"]; ?> | <?= $ini["general"]["app_name"]; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=JetBrains+Mono&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/v8.css?v=1">
</head>

<body>
    <div class="animated-bg"></div>

    <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/gui/standard_nav.php"; ?>

    <main style="max-width: 1080px; margin: 0 auto; padding: 2rem;">
        <h1><?= $loc["title"]; ?> | <?= $ini["general"]["app_name"]; ?></h1>

        <div class="card v8-bordered" style="margin-bottom: 2rem;">
            <h2><?= $loc["h2"]; ?></h2>
            <p><?= $loc["order"]; ?></p>

            <form method="POST" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" style="margin-top: 1rem;">
                <p><?= $loc["sorted"]; ?></p>

                <label><?= $loc["year"]; ?></label>
                <input type="number" name="jahr" min="2020" max="2030" value="<?= htmlspecialchars($date_year); ?>" required>

                <label style="margin-left: 1rem;"><?= $loc["month"]; ?></label>
                <input type="number" name="monat" min="1" max="12" value="<?= htmlspecialchars($date_month); ?>" required>

                <button type="submit" style="margin-left: 1rem;">
                    <?= $loc["search"]; ?>
                </button>
            </form>
        </div>

        <div class="card v8-bordered">
            <div class="table-wrapper">
                <table class="v8-table">
                    <thead>
                        <tr>
                            <th><?= $loc["employee"]; ?></th>
                            <th><?= $loc["sday"]; ?></th>
                            <th><?= $loc["sbegin"]; ?></th>
                            <th><?= $loc["send"]; ?></th>
                            <th><?= $loc["pbegin"]; ?></th>
                            <th><?= $loc["pend"]; ?></th>
                            <th><?= $loc["loc"]; ?></th>
                            <th><?= $loc["type"]; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?= $arbeit->get_specific_worktime_html($date_month, $date_year); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/gui/standard_footer.php"; ?>
</body>
</html>
