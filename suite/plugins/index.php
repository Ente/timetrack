<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/class/plugins/loader.plugins.arbeit.inc.php";
use Arbeitszeit\PluginBuilder;
use Arbeitszeit\Arbeitszeit;
$arbeit = new Arbeitszeit;
$arbeit->auth()->login_validation();
$pl = new PluginBuilder();
$pl->redirect_if_disabled();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Plugin Hub | <?= $arbeit->get_app_ini()["general"]["app_name"]; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=JetBrains+Mono&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="<?= $arbeit->benutzer()->loadUserTheme(); ?>?v=1">
</head>

<body>
    <div class="animated-bg"></div>

    <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/gui/standard_nav.php"; ?>

    <main style="max-width: 960px; margin: 0 auto; padding: 2rem;">
        <h1>🧩 Plugin Hub</h1>

        <nav class="plugin-nav v8-bordered">
            <strong>🧩 Plugin Navigation</strong>

            <div class="card v8-bordered" style="margin-bottom: 2rem;">
                <?php include __DIR__ . "/nav.php"; ?>
            </div>
            <div class="card v8-bordered">
                <?php
                if (!isset($_GET["pn"])) {
                    echo "<div class='status-message warn'>
                        <span class='dismiss-button' onclick='this.parentElement.classList.add(\"dismissed\")'>&times;</span>
                        No plugin selected.
                      </div>";
                } else {
                    $pluginName = $arbeit->i18n()->sanitizeOutput($_GET["pn"]);
                    $pluginView = $arbeit->i18n()->sanitizeOutput($_GET["p_view"]);

                    $f = $pl->load_plugin_view($pluginName, $pluginView);

                    if ($f === false) {
                        echo "<div class='status-message error'>
                            <span class='dismiss-button' onclick='this.parentElement.classList.add(\"dismissed\")'>&times;</span>
                            An error occurred while getting the requested view!<br>
                            Probably it simply does not exist or could not be imported.<br>
                            Check the log for details.
                          </div>";

                        echo "<div class='v8-bordered' style='margin-top: 1rem;'><strong>Request Details:</strong><pre style='margin-top:0.5rem;'>" . print_r($_GET, true) . "</pre></div>";
                    }
                }
                ?>
            </div>
    </main>

    <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/gui/standard_footer.php"; ?>
</body>

</html>