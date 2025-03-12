<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/class/auth/plugins/mail_password_reset.auth.arbeit.inc.php";
use Arbeitszeit\Arbeitszeit;
$arbeitszeit = new Arbeitszeit;
$ini = $arbeitszeit->get_app_ini();
$loc = $arbeitszeit->i18n()->loadLanguage(NULL, "reset");
echo $arbeitszeit->check_status_code($_SERVER["REQUEST_URI"]);

?>
<!DOCTYPE html>
<html data-bs-theme="dark" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title><?php echo $loc["title"] ?> | <?php echo $ini["general"]["app_name"]; ?></title>
    <link rel="stylesheet" href="/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/Dark-Mode-Switch.css">
    <link rel="stylesheet" href="/assets/css/Navbar-Centered-Brand-icons.css">
    <link rel="stylesheet" href="/assets/css/Pretty-Registration-Form-.css">
</head>

<body>
    <section class="position-relative py-4 py-xl-5">
        <div class="container position-relative">
            <div class="row d-flex justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5 col-xxl-6">
                    <div class="card mb-5">
                        <div class="card-body p-sm-5">
                            <h2 class="text-center mb-4"><?php echo $loc["title_q"] ?></h2>
                            <p class="text-center mb-4"><?php echo $loc["request_mail"] ?></p>
                            <form method="post" action="actions/auth/reset.php">
                                <div class="mb-3"><input class="form-control" type="email" id="email-2" name="email" placeholder="<?php echo $loc["label_email"] ?>"></div>
                                <div><button class="btn btn-primary d-block w-100" type="submit"><?php echo $loc["label_button"] ?></button></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <footer class="text-center py-4">
        <div class="container" style="max-height: none;">
            <div class="row row-cols-1 row-cols-lg-3">
                <div class="col">
                    <p class="text-muted my-2"><?php echo $ini["general"]["app_name"]; ?> - <?php echo date("Y") ?> -&nbsp;<a href="https://github.com/Ente/timetrack/releases/tag/v<?php echo @file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/VERSION"); ?>" target="_blank" rel="external">TimeTrack</a></p>
                </div>
                <div class="col"></div>
                <div class="col">
                    <ul class="list-inline my-2">
                        <li class="list-inline-item"><a class="link-secondary" href="#">Privacy Policy</a></li>
                        <li class="list-inline-item"><a class="link-secondary" href="#">Cookies</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
    <script src="../assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="../assets/js/bs-init.js"></script>
    <script src="../assets/js/Dark-Mode-Switch-darkmode.js"></script>
</body>

</html>