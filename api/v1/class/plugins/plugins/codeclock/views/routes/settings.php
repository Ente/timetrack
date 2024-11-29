<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/vendor/autoload.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/class/plugins/loader.plugins.arbeit.inc.php";
require_once dirname(__DIR__, 2) . "/src/Main.php";
require_once dirname(__DIR__, 2) . "/src/Code.php";
require_once dirname(__DIR__, 2) . "/src/Setup.php";
session_start();
use Arbeitszeit\Arbeitszeit;
use CodeClock\codeclock;
use CodeClock\Code;

$arbeit = new Arbeitszeit;
$main = new codeclock;
$code = new Code;

$link_logout = "http://" . $arbeit->get_app_ini()["general"]["base_url"] . "/api/v1/toil/code?logout=true";

$worktimeStatus = [
    "link" => "http://" . $arbeit->get_app_ini()["general"]["base_url"] . "/api/v1/toil/code?settings=true&worktime_start=true",
    "action" => "Start Worktime"
];
$active = Arbeitszeit::check_easymode_worktime_finished($code->getUserbyPIN($_SESSION["codeclock_pin"]));
$worktime = Arbeitszeit::get_worktime_by_id($active);


if(isset($_GET["worktime_start"])){
    $arbeit->add_easymode_worktime($code->getUserbyPIN($_SESSION["codeclock_pin"]));
    $status ='<div class="alert alert-success" role="alert"><span><strong>Success:</strong> Worktime started successfully.</span></div>';
    header("Location: http://" . $arbeit->get_app_ini()["general"]["base_url"] . "/api/v1/toil/code?settings=true");
}

if(isset($_GET["worktime_end"])){
    $arbeit->end_easymode_worktime($code->getUserbyPIN($_SESSION["codeclock_pin"]), $active);
    $status ='<div class="alert alert-success" role="alert"><span><strong>Success:</strong> Worktime ended successfully.</span></div>';
    header("Location: http://" . $arbeit->get_app_ini()["general"]["base_url"] . "/api/v1/toil/code?settings=true");
}


if($active == -1){
    // start
    $worktimeStatus = [
        "link" => "http://" . $arbeit->get_app_ini()["general"]["base_url"] . "/api/v1/toil/code?settings=true&worktime_start=true",
        "action" => "Start Worktime"
    ];
} else {
    // end
    $worktimeStatus = [
        "link" => "http://" . $arbeit->get_app_ini()["general"]["base_url"] . "/api/v1/toil/code?settings=true&worktime_end=true",
        "action" => "End Worktime"
    ];
}

if(isset($_SESSION["codeclock_pin"])){
    if(@$code->validatePIN($code->getUserbyPIN($_SESSION["codeclock_pin"]), $_SESSION["codeclock_pin"])){
        $_SESSION["codeclock"] = true;
    } else {
        $status ='<div class="alert alert-danger" role="alert"><span><strong>Wrong credentials:</strong> Your password is incorrect. Please try again. If you don\'t remember it, please login normally and look it up.</span></div>';
        echo "<script>alert('Invalid PIN');</script>";
        header("Location: http://" . $arbeit->get_app_ini()["general"]["base_url"] . "/api/v1/toil/code?unauth=true");
    }
}

?>
<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Settings</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/styles.min.css">
</head>

<body>
    <section class="position-relative py-4 py-xl-5">
        <div class="container position-relative">
            <div class="row d-flex justify-content-center">
                <div class="col-md-10 col-lg-8 col-xl-6 col-xxl-5">
                    <div class="card mb-5">
                        <div class="card-body p-sm-5">
                            <?php echo @$status; ?>
                            <h2 class="text-center mb-4">CodeClock Plugin - Settings</h2>
                            <div>
                                <p>Status:&nbsp;<span class="text-center"><?php echo $worktimeStatus["action"] ?></span></p><p>Current ID: <span><?php if($active == -1){ $active = "N/A";} echo $active ?></span></p>
                                <p>Username:&nbsp;<span class="text-center"><?php echo $arbeit->benutzer()->get_user($code->getUserbyPIN($_SESSION["codeclock_pin"]))["username"] ?></span></p>
                            </div>
                            <form method="post">
                                <div class="mb-3"></div>
                                <div><a class="btn btn-primary d-block w-100" role="button" href="<?php echo $worktimeStatus["link"] ?>"><?php echo $worktimeStatus["action"] ?></a></div><br>
                            </form><a class="btn btn-warning" role="button" style="width: 100%;" href="<?php echo $link_logout; ?>">Logout</a>
                            <br>
                            <p class="text-muted">CodeClock Plugin - <?php echo $arbeit->get_app_ini()["general"]["app_name"]; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>