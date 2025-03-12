<?php
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
$arbeit = new Arbeitszeit;
$ini = $arbeit->get_app_ini();
$base_url = $ini["general"]["base_url"];
echo $arbeit->check_status_code($_SERVER["REQUEST_URI"]);
$language = $arbeit->i18n()->loadLanguage(NULL, "login");
?>

<!DOCTYPE html>
<html data-bs-theme="dark" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title><?php echo $language["title"] ?> | <?php echo $ini["general"]["app_name"]; ?></title>
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
                            <h2 class="text-center mb-4"><?php echo $language["h1"] ?></h2>
                            <form method="post" action="actions/auth/login.php">
                                <div class="mb-3"><input class="form-control" type="text" id="email-1" name="username" placeholder="<?php echo $language["placeholder_username"] ?>" style="margin-bottom: 5px;"><input class="form-control" type="password" id="email-2" name="password" placeholder="<?php echo $language["placeholder_password"] ?>" style="margin-bottom: 5px;"></div>
                                <div class="form-check"><input class="form-check-input" type="checkbox" id="formCheck-1" name="erinnern"><label class="form-check-label" for="formCheck-1"><?php echo $language["checkbox_30days"] ?></label></div>
                                <div><button class="btn btn-primary d-block w-100" type="submit"><?php echo $language["button_text"] ?></button></div>
                            </form>
                        </div>
                        <a href="forgot_password.php"><?php echo $language["forgot_pw"] ?></a>
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