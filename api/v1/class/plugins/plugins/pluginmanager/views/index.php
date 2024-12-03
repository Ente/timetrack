<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/class/plugins/PluginBuilder.plugins.arbeit.inc.php";
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/v1/class/plugins/plugins/pluginmanager/src/Main.php';
use pluginmanager\pluginmanager;
use Arbeitszeit\Arbeitszeit;

$main = new pluginmanager();
$arbeit = new Arbeitszeit();
$arbeit->auth()->login_validation();
if(!$arbeit->benutzer()->is_admin($arbeit->benutzer()->get_user($_SESSION["username"]))){
    die();
}

if(@isset($_GET["edit"])){
    require_once __DIR__ . "/edit.php";
    die();
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
                                <div id="plugins">
                                    <?php foreach($main->getPluginsHtml() as $html){
                                        echo $html;
                                    } ?>
                                <div id="reload"><a class="btn btn-primary d-block w-100" href="/api/v1/toil/pluginmanager" style="margin: 5px;">Reload</a></div>
                            <p class="text-center text-muted" id="footer">Plugin Manager - <?php echo $arbeit->get_app_ini()["general"]["app_name"]; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>