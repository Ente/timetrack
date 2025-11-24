<?php 
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/class/plugins/loader.plugins.arbeit.inc.php";
require_once dirname(__DIR__, 2) . "/src/Main.php";
session_start();

use Arbeitszeit\Arbeitszeit;
use NFClogin\NFClogin;
use NFCClock\NFCClock;

$arbeit = new Arbeitszeit;
$status = '';
if(isset($_GET["logout"])){
    session_destroy();
    header("Location: http://" . $arbeit->get_app_ini()["general"]["base_url"] . "/api/v1/toil/nfcclock");
}

if(isset($_GET["nosession"])){
    $status ='<div class="alert alert-danger" role="alert"><span><strong>No card or invalid:</strong> Either no card was scanned or it is not mapped to a user.</span></div>';

}
?>
<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>NFCClock - <?= $arbeit->get_app_ini()["general"]["app_name"] ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css">
</head>

<body>
    <section class="position-relative py-4 py-xl-5">
        <div class="container">
            <div class="row mb-5">
                <div class="col-md-8 col-xl-6 text-center mx-auto">
                    <?php echo @$status; ?>
                    <h2>NFCClock Login</h2>
                    <p class="w-lg-50">Please click on the login button to log in with your NFC tag.<br>Hold it close to the NFC reader.</p>
                </div>
            </div>
            <div class="row d-flex justify-content-center">
                <div class="col-md-6 col-xl-4">
                    <div class="card mb-5">
                        <div class="card-body d-flex flex-column align-items-center">
                            <form class="text-center" method="post">
                                <div class="mb-3"><a class="btn btn-primary w-100 d-block" href="/api/v1/toil/nfclclock">Login</a></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>