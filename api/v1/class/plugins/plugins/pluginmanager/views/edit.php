<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/class/plugins/PluginBuilder.plugins.arbeit.inc.php";
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/v1/class/plugins/plugins/pluginmanager/src/Main.php';
use pluginmanager\pluginmanager;
use Arbeitszeit\Arbeitszeit;

$main = new pluginmanager();
$arbeit = new Arbeitszeit();
if(@isset($_GET["edit"])){
    try {
        $yaml = $main->getPluginYaml($_GET["edit"]);
    } catch (TypeError $e){
        $yaml = "An error occured while trying to retrieve the YAML configuration. Does this plugin exist?";
        $status = "<div class='alert alert-danger' role='alert'>Plugin not found!</div>";
        $submitButton = "";
        $submitEdit = "";
        goto skip_html;
    }
    if($_GET["edit"] == "pluginmanager"){
        $status = "<div class='alert alert-danger' role='alert'><strong>You cannot edit the Plugin Manager!</strong> If you really need to, do this on the server directly.</div>";
        $submitEdit = "";
        $submitButton = "<button class='btn btn-primary' type='submit' disabled>Save</button>";
        goto skip_get;
    } else {
        $submitEdit = "<input type='hidden' name='edit' value='{$_GET["edit"]}'>";
        $submitButton = "<button class='btn btn-primary' type='submit'>Save</button>";
    }
    if(@isset($_GET["disabled"])){
        $main->disablePlugin($_GET["edit"]);
        $status = "<div class='alert alert-success' role='alert'>Plugin disabled! You may want to reload the windows for the YAML configuration to reload if it still shows 'false'.</div>";
    }
    if(@isset($_GET["enabled"])){
        $main->enablePlugin($_GET["edit"]);
        $status = "<div class='alert alert-success' role='alert'>Plugin enabled! You may want to reload the windows for the YAML configuration to reload if it still shows 'false'.</div>";
    }
    skip_get:
    $isEnabled = $main->pluginIsEnabledHtml($_GET["edit"]);
    $isDisabled = $main->pluginIsDisabledHtml($_GET["edit"]);
    skip_html:
}

?><!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>pluginmanager-plugin-timetrack</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>

<body>
    <section class="position-relative py-4 py-xl-5">
        <div class="container position-relative">
            <div class="row d-flex justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5 col-xxl-9">
                    <div class="card mb-5">
                        <div class="card-body p-sm-5">
                            <h2 class="text-center mb-4">Plugin Manager</h2>
                            <?php echo @$status; ?>
                            <form method="get">
                                <div id="plugins">
                                    <p>Plugin Configuration:</p>
                                </div><code><pre><?php echo $yaml; ?></pre></code>
                                <div id="reload">
                                    <p><label class="form-label">Enabled?</label><input type="checkbox" name="enabled" <?php echo $isEnabled; ?>></p>
                                    <p><label>Disable? <input type="checkbox" name="disabled" <?php echo $isDisabled; ?>></label></p>
                                    <?php
                                    echo $submitEdit;
                                    echo $submitButton;
                                    ?>
                                </div>
                            </form>
                            <p class="text-center text-muted" id="footer">Plugin Manager - <?php echo $arbeit->get_app_ini()["general"]["app_name"]; ?> - <a href="/api/v1/toil/pluginmanager">Get back</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>