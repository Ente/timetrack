<?php
session_start();
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
use Arbeitszeit\Arbeitszeit;
$arbeit = new Arbeitszeit;
$ini = $arbeit->get_app_ini();
$base_url = $ini["general"]["base_url"];

$language = $arbeit->i18n()->loadLanguage(null, "users/edit", "admin");

$arbeit->auth()->login_validation();
if(!$arbeit->benutzer()->is_admin($arbeit->benutzer()->get_user($_SESSION["username"]))){
    header("Location: http://{$base_url}/suite/?" . $arbeit->statusMessages()->URIBuilder("noperms"));
}
$add_style = "";


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $language["title"]; ?> | <?= $ini["general"]["app_name"]; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=JetBrains+Mono&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/v8.css?v=1">
</head>

<body>
    <div class="animated-bg"></div>

    <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/gui/standard_nav.php"; ?>

    <main style="max-width: 1080px; margin: 0 auto; padding: 2rem;">
        <h1><?= $language["title"]; ?></h1>

        <!-- Einleitung -->
        <div class="card v8-bordered" style="margin-bottom: 2rem;">
            <p><?= $language["p1"]; ?></p>
        </div>

        <!-- Nutzerliste -->
        <div class="card v8-bordered" style="margin-bottom: 2rem;">
            <h2><?= $language["title"]; ?></h2>
            <div class="table-wrapper">
                <table class="v8-table">
                    <thead>
                        <tr>
                            <th><?= $language["th1"]; ?></th>
                            <th><?= $language["th2"]; ?></th>
                            <th><?= $language["th3"]; ?></th>
                            <th><?= $language["th4"]; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $arbeit->benutzer()->get_all_users_html(); ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Benutzer hinzufügen -->
        <div class="card v8-bordered">
            <h2><?= $language["add_user"]; ?></h2>

            <form action="../../admin/actions/users/add_user.php" method="POST">
                <label title="Der Username besteht aus Anfangsbuchstabe des Vornamens großgeschrieben und voller Nachname, z. B. MMueller">
                    Username:
                </label>
                <input type="text" name="username" placeholder="MMustermann" required>
                <br>
                <label><?= $language["label_firstname"]; ?>:</label>
                <input type="text" name="name" placeholder="Name" required>
                <br>
                <label><?= $language["label_email"]; ?>:</label>
                <input type="email" name="email" placeholder="email@example.com" required>
                <br>
                <label><?= $language["label_password"]; ?>:</label>
                <input type="password" name="password" placeholder="••••••••" required>
                <br>
                <label style="display: inline-block; margin-top: 1rem;">
                    <input type="checkbox" name="admin" value="true">
                    <?= $language["label_grant_admin"]; ?>
                </label>
                <br>
                <button type="submit" name="submit" style="margin-top: 1rem;" <?= $add_style ?? ""; ?>>
                    <?= $language["button_text"]; ?>
                </button>
            </form>
        </div>
    </main>

    <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/gui/standard_footer.php"; ?>
</body>
</html>
