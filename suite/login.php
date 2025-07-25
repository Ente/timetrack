<?php

require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/class/plugins/loader.plugins.arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
$arbeit = new Arbeitszeit;
use Arbeitszeit\PluginBuilder;
use NFClogin\NFClogin;
$ini = $arbeit->get_app_ini();
$base_url = $ini["general"]["base_url"];
$language = $arbeit->i18n()->loadLanguage(NULL, "login");

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?= $language["title"]; ?> | <?= $ini["general"]["app_name"]; ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=JetBrains+Mono&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/v8.css?v=1">
</head>
<body>
  <div class="animated-bg"></div>
  
  <main style="display:flex;justify-content:center;align-items:center;min-height:100vh;flex-direction:column;">
    <div class="card" style="max-width: 400px; width: 100%;">
      <h2 class="text-center"><?= $language["h1"] ?></h2>
      <form method="post" action="actions/auth/login.php">
        <input type="text" name="username" placeholder="<?= $language["placeholder_username"]; ?>" required>
        <input type="password" name="password" placeholder="<?= $language["placeholder_password"]; ?>" required><br>
        <label><input type="checkbox" name="erinnern"> <?= $language["checkbox_30days"]; ?></label>
        <button type="submit"><?= $language["button_text"]; ?></button>
      </form>
      <?php
        $pl = new PluginBuilder();
        if($pl->read_plugin_configuration("nfclogin")["enabled"] == "true"){
            require_once dirname(__DIR__, 1) . "/api/v1/class/plugins/plugins/nfclogin/src/Main.php";
            $nfc = new NFClogin;
            echo "<br>" . $nfc->nfcloginHtml() . "<br>";
        }
        ?>
    </div>
    <p style="margin-top: 1rem;"><a href="forgot_password.php"><?= $language["forgot_pw"]; ?></a></p>
  </main>

    <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/gui/standard_footer.php"; ?> 

</body>
</html>