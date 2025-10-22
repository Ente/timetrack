<?php
require dirname(__DIR__, 4) . "/api/v1/inc/arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
$arbeit = new Arbeitszeit;
$ini = $arbeit->get_app_ini();
$base_url = $ini["general"]["base_url"];
$loc = $arbeit->i18n()->loadLanguage(null, "worktime/vacation/all", "admin");
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
    <link rel="stylesheet" href="<?= $arbeit->benutzer()->loadUserTheme(); ?>?v=1">
</head>

<body>
    <div class="animated-bg"></div>

    <?php include dirname(__DIR__, 4) . "/assets/gui/standard_nav.php"; ?>

    <main style="max-width: 1080px; margin: 0 auto; padding: 2rem;">
        <h1><?= $loc["title"]; ?> | <?= $ini["general"]["app_name"]; ?></h1>

        <div class="card v8-bordered">
            <h2><?= $loc["note1"]; ?></h2>
            <p><?= $loc["note2"]; ?></p>

            <div class="table-wrapper" style="margin-top: 1rem;">
                <table class="v8-table">
                    <thead>
                        <tr>
                            <th><?= $loc["t1"]; ?></th>
                            <th><?= $loc["t2"]; ?></th>
                            <th><?= $loc["t3"]; ?></th>
                            <th><?= $loc["t4"]; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?= $arbeit->vacation()->display_vacation_all(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/gui/standard_footer.php"; ?>
</body>
</html>
