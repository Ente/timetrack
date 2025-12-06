<?php
session_start();
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
use Arbeitszeit\Arbeitszeit;
$arbeit = new Arbeitszeit;
$ini = $arbeit->get_app_ini();
$base_url = $ini["general"]["base_url"];

$arbeit->auth()->login_validation();
if(!$arbeit->benutzer()->is_admin($arbeit->benutzer()->get_user($_SESSION["username"]))){
    header("Location: http://{$base_url}/suite/?" . $arbeit->statusMessages()->URIBuilder("noperms"));
}
$add_style = "";

if(!$arbeit->telemetry()->isTelemetryServerEnabled()){
    $arbeit->statusMessages()->redirect("telemetryServer_disabled");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Server Telemetry | <?= $ini["general"]["app_name"]; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=JetBrains+Mono&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $arbeit->benutzer()->loadUserTheme(); ?>?v=1">
</head>

<body>
    <div class="animated-bg"></div>

    <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/gui/standard_nav.php"; ?>

    <main style="max-width: 1080px; margin: 0 auto; padding: 2rem;">
        <h1>Server Telemetry</h1>

        <div class="card v8-bordered" style="margin-bottom: 2rem;">
            <p>On this page you can see all of the received telemetry for your servers.</p>
        </div>

        <div class="card v8-bordered" style="margin-bottom: 2rem;" id="userlist">
            <h2>Statistics</h2>
            <div class="table-wrapper">
                <?php $arbeit->telemetry()->renderServerTelemetryHTML(); ?>
            </div>
        </div>

        
    </main>

    <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/gui/standard_footer.php"; ?>
</body>
</html>
