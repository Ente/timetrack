<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/v1/class/plugins/plugins/exportmanager/src/Main.php';
use Arbeitszeit\Arbeitszeit;

?>
<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>exportmanager-plugin-timetrack</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>

<body class="text-center">
    <div></div>
    <h1>Export Manager Plugin - Help</h1>
    <section class="position-relative py-4 py-xl-5">
        <div class="container position-relative">
            <div class="row d-flex justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5 col-xxl-8">
                    <div class="card mb-5">
                        <div class="card-body p-sm-5">
                            <h2 class="text-center mb-4">Help</h2>
                            <div class="accordion" role="tablist" id="accordion-1">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" role="tab"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accordion-1 .item-1" aria-expanded="false" aria-controls="accordion-1 .item-1">How does this work?</button></h2>
                                    <div class="accordion-collapse collapse item-1" role="tabpanel" data-bs-parent="#accordion-1">
                                        <div class="accordion-body">
                                            <p class="mb-0">This plugin uses the&nbsp;<span style="font-family: monospace;">ExportManager</span>&nbsp;class to determine all available export modules.<br>If the export module contains a <span style="font-family: monospace;">exportManagerPluginConfig.json</span>&nbsp;this one will be read and allows interaction with this plugin.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" role="tab"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accordion-1 .item-2" aria-expanded="false" aria-controls="accordion-1 .item-2">Add own&nbsp;<span style="font-family: monospace;">exportManagerPluginConfig.json</span>  (WIP)</button></h2>
                                    <div class="accordion-collapse collapse item-2" role="tabpanel" data-bs-parent="#accordion-1">
                                        <div class="accordion-body">
                                            <p class="mb-0">To Add your own plugin configuration file to your exportModule, please make sure to set the following attributes:</p>
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>Attribute</th>
                                                            <th>Description</th>
                                                            <th>Example Value</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>name</td>
                                                            <td>Plugin name to display</td>
                                                            <td>CSV export module</td>
                                                        </tr>
                                                        <tr>
                                                            <td>description</td>
                                                            <td>Plugin description to display</td>
                                                            <td>Manage CSV exports</td>
                                                        </tr>
                                                        <tr>
                                                            <td>actions</td>
                                                            <td>Array containing "key" =&gt; "value" pairs</td>
                                                            <td>...</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" role="tab"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accordion-1 .item-3" aria-expanded="false" aria-controls="accordion-1 .item-3">How to export files?</button></h2>
                                    <div class="accordion-collapse collapse item-3" role="tabpanel" data-bs-parent="#accordion-1">
                                        <div class="accordion-body">
                                            <p class="mb-0">Log into your server with SSH and download them e.g. via WinSCP. Or customize the output directory to e.g. a mount. You are still able to download the files directly from your browser.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p>Export Manager Plugin - <?php echo Arbeitszeit::get_app_ini()["general"]["app_name"] ?> -&nbsp;<a href="/api/v1/toil/exportmanager">List</a></p>
                </div>
            </div>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>