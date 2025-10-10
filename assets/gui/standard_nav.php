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
$lang = $i18n->loadLanguage(null, "nav");
?>
<nav class="topnav">
  <div class="topnav-left">
    <a href="http://<?= $base_url ?>/suite"><?= $lang["menu"]; ?></a>
    <a href="http://<?= $base_url ?>/suite/users/settings.php"><?= $lang["settings"]; ?></a>
    <a href="http://<?= $base_url ?>/suite/worktime/all.php"><?= $lang["own_worktime"]; ?></a>
    <a href="http://<?= $base_url ?>/suite/notifications/all.php"><?= $lang["notifications"]; ?></a>
    <a href="http://<?= $base_url ?>/suite/projects/overview.php"><?= $lang["projects"] ?></a>

    <?php if (@$user->is_Admin($user->get_user(@$_SESSION["username"]))) : 
        $v = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/VERSION"); ?>
        <a href="http://<?= $base_url ?>/suite/admin/worktime/all.php"><?= $lang["a_allworktime"]; ?></a>
        <a href="http://<?= $base_url ?>/suite/admin/users/edit.php"><?= $lang["a_useredit"]; ?></a>
        <a href="http://<?= $base_url ?>/suite/admin/worktime/sick/all.php"><?= $lang["a_sickness"]; ?></a>
        <a href="http://<?= $base_url ?>/suite/admin/worktime/vacation/all.php"><?= $lang["a_vacation"]; ?></a>
        <a href="http://<?= $base_url ?>/suite/admin/projects/admin.php"><?= $lang["a_projects"]; ?></a>
        <a href="http://<?= $base_url ?>/suite/plugins/index.php" target="_blank"><?= $lang["a_plugins"]; ?></a>
        <span class="nav-version">ADMIN | <?= htmlspecialchars($v); ?></span>
    <?php endif; ?>
  </div>
  <div class="topnav-right">
    <span class="user-label"><?= htmlspecialchars($name); ?></span>
    <a href="http://<?= $base_url ?>/suite/actions/auth/logout.php"><?= $lang["logout"]; ?></a>
  </div>
</nav>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cookie-banner/1.2.2/cookiebanner.min.js" id="cookiebanner"></script>
