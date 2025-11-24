<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/class/plugins/loader.plugins.arbeit.inc.php";
require_once dirname(__DIR__, 1) . "/src/Main.php";
session_start();
use Arbeitszeit\Arbeitszeit;
use NFCClock\NFCClock;
$arbeit = new Arbeitszeit;
$main = new NFCClock;
?>
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12 text-center">
            <h1>NFCClock Plugin</h1>
            <p class="lead">Welcome to the NFCClock plugin for TimeTrack!</p>
            <p>This plugin allows you to clock in and out using NFC cards.</p>
            <p><a href="/api/v1/toil/nfcclock" class="btn btn-primary">Go to Login Page</a> or type in http://<?php echo $arbeit->get_app_ini()["general"]["base_url"]  ?>/api/v1/toil/nfcclock</p>
        </div>
    </div>