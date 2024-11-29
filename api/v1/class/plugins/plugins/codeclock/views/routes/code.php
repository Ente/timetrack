<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/vendor/autoload.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/class/plugins/loader.plugins.arbeit.inc.php";
require_once dirname(__DIR__, 2) . "/src/Main.php";
require_once dirname(__DIR__, 2) . "/src/Code.php";
require_once dirname(__DIR__, 2) . "/src/Setup.php";

use Arbeitszeit\Arbeitszeit;
use CodeClock\codeclock;
use CodeClock\Code;

$arbeit = new Arbeitszeit;
$main = new codeclock;
$code = new Code;

if(isset($_GET["logout"])){
    session_destroy();
    header("Location: http://" . $arbeit->get_app_ini()["general"]["base_url"] . "/api/v1/toil/code");
}

if(isset($_GET["pin"])){
    if(@$code->validatePIN($code->getUserbyPIN($_GET["pin"]), $_GET["pin"])){
        session_start();
        $_SESSION["codeclock"] = true;
        $_SESSION["codeclock_pin"] = $_GET["pin"];
        header("Location: http://" . $arbeit->get_app_ini()["general"]["base_url"] . "/api/v1/toil/code?settings=true");
    } else {
        $status ='<div class="alert alert-danger" role="alert"><span><strong>Wrong credentials:</strong> Your password is incorrect. Please try again. If you don\'t remember it, please login normally and look it up.</span></div>';
        echo "<script>alert('Invalid PIN');</script>";
    }
}

if(isset($_GET["settings"])){
    require_once __DIR__ . "/settings.php";
    die();
}

if(isset($_GET["unauth"])){
    $status ='<div class="alert alert-danger" role="alert"><span><strong>Wrong credentials:</strong> Your password is incorrect. Please try again. If you don\'t remember it, please login normally and look it up.</span></div>';

}
?>
<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/styles.min.css">
</head>

<body>
    <section class="position-relative py-4 py-xl-5">
        <div class="container">
            <div class="row mb-5">
                <div class="col-md-8 col-xl-6 text-center mx-auto">
                    <?php echo @$status; ?>
                    <h2>CodeClock Login</h2>
                    <p class="w-lg-50">Please enter your PIN to login. You can view your PIN within the Pluginhub &gt; [codeclock] View PIN view.</p>
                </div>
            </div>
            <div class="row d-flex justify-content-center">
                <div class="col-md-6 col-xl-4">
                    <div class="card mb-5">
                        <div class="card-body d-flex flex-column align-items-center">
                            <form class="text-center" method="get" action="">
                                <div class="mb-3"><input class="form-control" type="password" name="pin" placeholder="PIN"></div>
                                <div class="mb-3"><button class="btn btn-primary d-block w-100" type="submit">Login</button></div>
                                <p class="text-muted">CodeClock Plugin - <?php echo $arbeit->get_app_ini()["general"]["app_name"]; ?></p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>