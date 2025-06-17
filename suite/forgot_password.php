<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
use Arbeitszeit\Arbeitszeit;
$arbeitszeit = new Arbeitszeit;
$ini = $arbeitszeit->get_app_ini();
$loc = $arbeitszeit->i18n()->loadLanguage(NULL, "reset");
?>
<!DOCTYPE html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $loc["title"]; ?> | <?= $ini["general"]["app_name"]; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Fonts & Style -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=JetBrains+Mono&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/v8.css?v=1">
</head>

<body>
    <div class="animated-bg"></div>

    <main style="min-height: 100vh; display: flex; align-items: center; justify-content: center;">
        <form class="card" action="actions/auth/reset.php" method="POST" style="max-width: 420px; width: 100%;">
            <h2 style="margin-bottom: 1rem;"><?= $loc["title_q"]; ?></h2>
            <p style="margin-bottom: 1.5rem;"><?= $loc["request_mail"]; ?></p>

            <label for="email"><?= $loc["label_email"]; ?></label>
            <input type="email" name="email" id="email" placeholder="you@mail.com" required>

            <button type="submit" name="reset" value="true" style="margin-top: 1rem;">
                <?= $loc["label_button"]; ?>
            </button>
        </form>
    </main>

    <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/gui/standard_footer.php"; ?> 

</body>
</html>
