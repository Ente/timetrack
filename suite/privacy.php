<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
use Arbeitszeit\Arbeitszeit;
$arbeitszeit = new Arbeitszeit;
$ini = $arbeitszeit->get_app_ini();
$loc = $arbeitszeit->i18n()->loadLanguage(NULL, "privacy");
?>
<?php
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="utf-8">
  <title><?= $loc["h1"]; ?> | <?= $ini["general"]["app_name"]; ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=JetBrains+Mono&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/v8.css?v=1">
</head>
<body>
  <div class="animated-bg"></div>

  <main style="display:flex;justify-content:center;align-items:center;min-height:100vh;flex-direction:column;padding:1rem;">
    <div class="card" style="max-width: 800px; width: 100%;">
      <h2 class="text-center"><?= $loc["h1"] ?></h2>
      <p><?= $loc["intro"] ?></p>
      <br>
      <ul>
        <li><?= $loc["email_usage"] ?></li>
        <li><?= $loc["plugins"] ?></li>
        <li><?= $loc["locality"] ?></li>
      </ul>
    </div>

    <div class="card" style="max-width: 800px; width: 100%; margin-top: 1rem;">
      <h3><?= $loc["contact"] ?></h3>
      <p><?= $loc["contact_note"] ?><br>
        <a href="mailto:<?= $ini["general"]["support_email"]; ?>">
          <?= $ini["general"]["support_email"]; ?>
        </a>
      </p>
    </div>
  </main>

  <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/gui/standard_footer.php"; ?> 
</body>
</html>


