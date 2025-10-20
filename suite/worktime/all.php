<?php
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
$arbeit = new Arbeitszeit;
$ini = $arbeit->get_app_ini();
$loc = $arbeit->i18n()->loadLanguage(NULL, "worktime/all");
$arbeit->auth()->login_validation();
$date_year = date("Y");
$date_month = date("m");
if(!isset($_GET["jahr"])){
    $jahr = $date_year;
}
if(!isset($_GET["monat"])){
    $monat = $date_month;
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

    <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/gui/standard_nav.php"; ?>

    <main style="max-width: 1080px; margin: 0 auto; padding: 2rem;">
        <h1><?= $loc["title"]; ?> | <?= $ini["general"]["app_name"]; ?></h1>

        <div class="card" style="padding: 1rem 2rem;">
            <h2><?= $loc["h2"]; ?></h2>

            <div class="table-wrapper">
                <table class="v8-table">
                    <thead>
                        <tr>
                            <th><?= $loc["s_day"]; ?></th>
                            <th><?= $loc["s_begin"]; ?></th>
                            <th><?= $loc["s_end"]; ?></th>
                            <th><?= $loc["s_pstart"]; ?></th>
                            <th><?= $loc["s_pend"]; ?></th>
                            <th><?= $loc["s_location"]; ?></th>
                            <th><?= $loc["type"]; ?></th>
                            <th><?= $loc["id"]; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?= $arbeit->get_employee_worktime_html($_SESSION["username"]); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

        <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/gui/standard_footer.php"; ?> 

</body>
</html>
