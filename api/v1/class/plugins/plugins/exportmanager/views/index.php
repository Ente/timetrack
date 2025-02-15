<?php

require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/class/plugins/PluginBuilder.plugins.arbeit.inc.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/class/plugins/plugins/exportmanager/src/Main.php";

use ExportManager\exportmanager;
use Arbeitszeit\Arbeitszeit;

$exportmanager = new exportmanager();
$arbeit = new Arbeitszeit();

$arbeit->auth()->login_validation();

if(isset($_GET["help"])){
    require_once __DIR__ . "/help.php";
    die();
}

if(isset($_GET["all"])){
    require_once __DIR__ . "/all.php";
    die();
}

if(isset($_GET["userall"])){
    require_once __DIR__ . "/userall.php";
    die();
}

if(isset($_GET["export"])){
    if(isset($_GET["plugin"])){
        if($_GET["action"] == "default"){
            $status ='<div class="alert alert-success" role="alert"><span><strong>Info:</strong> Your export is in progress...</span></div>';

            $exportmanager->defaultExport($_GET["plugin"]);
        } else {
            $status ='<div class="alert alert-danger" role="alert"><span><strong>Wrong "action" parameter:</strong> Currently only "default" is supported .</span></div>';
        }
    } else {
        $status ='<div class="alert alert-danger" role="alert"><span><strong>Missing "plugin" parameter:</strong> Please provide a plugin name.</span></div>';
    }
}



?><!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>exportmanager-plugin-timetrack</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>

<body class="text-center">
    <div>
        <h1>Export Manager Plugin</h1>
        <?php echo @$status; ?>
    </div>
    <section class="position-relative py-4 py-xl-5">
        <div class="container position-relative">
            <div class="row d-flex justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5 col-xxl-12">
                    <div class="card mb-5">
                        <div class="card-body p-sm-5">
                            <h2 class="text-center mb-4">All Plugins</h2>
                            <p>Here you can download your worktime sheets for the current month in any format available.</p>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Export Plugin</th>
                                            <th>Description</th>
                                            <th>Path</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <?php echo $exportmanager->getAllExportModulesHtml(); ?>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <p class="text-muted">Export Manager Plugin - <?php echo Arbeitszeit::get_app_ini()["general"]["app_name"]; ?> -&nbsp;<a href="?help=true">Help</a></p>
                    <p class="text-muted"><a href="?userall=true">View all my exports</a></p>
                    <?php
                    if($arbeit->benutzer()->is_admin($arbeit->benutzer()->get_user($_SESSION["username"]))){
                        echo '<p class="text-muted">Admin: <a href="?all=true">All Exports</a></p>';
                    } ?>
                    <p class="text-muted">Logged in as: <?php echo $_SESSION["username"]; ?></p>
                </div>
            </div>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>