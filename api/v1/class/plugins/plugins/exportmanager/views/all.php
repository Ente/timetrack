<?php

require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/class/plugins/PluginBuilder.plugins.arbeit.inc.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/class/plugins/plugins/exportmanager/src/Main.php";

use ExportManager\exportmanager;
use Arbeitszeit\Arbeitszeit;

$exportmanager = new exportmanager();
$arbeit = new Arbeitszeit();

$arbeit->auth()->login_validation();


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
    <div>
        <h1>Export Manager Plugin</h1>
    </div>
    <section class="position-relative py-4 py-xl-5">
        <div class="container position-relative">
            <div class="row d-flex justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5 col-xxl-12">
                    <div class="card mb-5">
                        <div class="card-body p-sm-5">
                            <h2 class="text-center mb-4">All available exports</h2>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Filename</th>
                                            <th>Extension</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php echo $exportmanager->generate_worktime_table($exportmanager->scan_worktime_sheets())  ?>
                                    </tbody>
                                </table>
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