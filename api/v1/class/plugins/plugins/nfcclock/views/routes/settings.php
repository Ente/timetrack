<?php 
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/class/plugins/loader.plugins.arbeit.inc.php";
require_once dirname(__DIR__, 2) . "/src/Main.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/class/plugins/plugins/nfclogin/src/Main.php";
session_start();

use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Benutzer;
use NFClogin\NFClogin;
use NFCClock\NFCClock;
$arbeit = new Arbeitszeit;
$main = new NFCClock;
$statusMessages = $arbeit->statusMessages();

// initial NFC login / session handling
if (!empty($_SESSION['nfcclock_user'])) {
    if(!Benutzer::get_user($_SESSION['nfcclock_user'])) {
        // invalid session user -> unset session and redirect to nfcclock
        unset($_SESSION['nfcclock_user']);
        header('Location: /api/v1/toil/nfcclock');
        exit;
    }
} elseif (!empty($_GET['uid']) && !empty($_GET['user'])) {
    $nfc = new NFClogin();
    $mappedUser = $nfc->getUser($_GET['uid']);
    if ($mappedUser === $_GET['user']) {
        // valid NFC -> set session
        $_SESSION['nfcclock_user'] = $_GET['user'];
    } else {
        // invalid mapping -> redirect to nfcclock with error and original uid/block if present
        $uri = $statusMessages->URIBuilder('wrongdata') ?? '';
        $params = ['uid=' . urlencode($_GET['uid'])];
        if (isset($_GET['block'])) {
            $params[] = 'block=' . urlencode($_GET['block']);
        }
        header('Location: /api/v1/toil/nfcclock?' . $uri . (empty($uri) ? '' : '&') . implode('&', $params));
        exit;
    }
} else {
    // no session and no uid/user in query -> redirect to nfcclock
    header('Location: /api/v1/toil/nfcclock?nosession=true');
    exit;
}

$link_logout = "http://" . $arbeit->get_app_ini()["general"]["base_url"] . "/api/v1/toil/nfcclock?logout=true";

$worktimeStatus = [
    "link" => "http://" . $arbeit->get_app_ini()["general"]["base_url"] . "/api/v1/toil/nfcclocksettings?worktime_start=true",
    "action" => "Start Worktime"
];
$active = Arbeitszeit::check_easymode_worktime_finished($_SESSION["nfcclock_user"]);
$worktime = Arbeitszeit::get_worktime_by_id($active);

if(isset($_GET["worktime_start"])){
    $arbeit->add_easymode_worktime($_SESSION["nfcclock_user"]);
    $status ='<div class="alert alert-success" role="alert"><span><strong>Success:</strong> Worktime started successfully.</span></div>';
    header("Location: http://" . $arbeit->get_app_ini()["general"]["base_url"] . "/api/v1/toil/nfcclocksettings");
}

if(isset($_GET["worktime_end"])){
    $arbeit->end_easymode_worktime($_SESSION["nfcclock_user"], $active);
    $status ='<div class="alert alert-success" role="alert"><span><strong>Success:</strong> Worktime ended successfully.</span></div>';
    header("Location: http://" . $arbeit->get_app_ini()["general"]["base_url"] . "/api/v1/toil/nfcclocksettings");
}

if($active == -1){
    // start
    $worktimeStatus = [
        "link" => "http://" . $arbeit->get_app_ini()["general"]["base_url"] . "/api/v1/toil/nfcclocksettings?worktime_start=true",
        "action" => "Start Worktime"
    ];
} else {
    // end
    $worktimeStatus = [
        "link" => "http://" . $arbeit->get_app_ini()["general"]["base_url"] . "/api/v1/toil/nfcclocksettings?worktime_end=true",
        "action" => "End Worktime"
    ];
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
        <div class="container position-relative">
            <div class="row d-flex justify-content-center">
                <div class="col-md-10 col-lg-8 col-xl-6 col-xxl-5">
                    <div class="card mb-5">
                        <div class="card-body p-sm-5">
                            <h2 class="text-center mb-4">NFCClock Plugin - Settings</h2>
                            <div>
                                <p>Status:&nbsp;<span class="text-center"><?= $worktimeStatus["action"] ?></span></p>
                                <p>Username:&nbsp;<span class="text-center"><?= $_SESSION["nfcclock_user"] ?></span></p>
                            </div>
                            <form method="post">
                                <div class="mb-3"></div>
                                <div><a class="btn btn-primary w-100 d-block" role="button" href="<?= $worktimeStatus["link"] ?>"><?= $worktimeStatus["action"] ?></a></div><br>
                            </form><a class="btn btn-warning" role="button" style="width: 100%;" href="<?= $link_logout ?>">Logout</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>