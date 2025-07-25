<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Benutzer;
use Arbeitszeit\i18n;

$arbeit = new Arbeitszeit;
$user = new Benutzer;
$i18n = new i18n;
$ini = $arbeit->get_app_ini();
$base_url = $ini["general"]["base_url"];
$name = $ini["general"]["app_name"];

$locale = locale_accept_from_http($_SERVER["HTTP_ACCEPT_LANGUAGE"]);
$lang = $i18n->loadLanguage(null, "footer");
?>

<footer class="footer glow-top">
        OpenDucks IT © <?= date("Y"); ?> —
        <a href="/suite/privacy.php" target="_blank"><?php echo $lang["privacy"] ?></a> ·
        <a href="mailto:<?= $ini["general"]["support_email"]; ?>"><?php echo $lang["support"]; ?></a>
    </footer>