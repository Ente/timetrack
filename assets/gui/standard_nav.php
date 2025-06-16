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
<script type="text/javascript" id="cookiebanner"
  src="https://cdnjs.cloudflare.com/ajax/libs/cookie-banner/1.2.2/cookiebanner.min.js"></script>
<ul>
    <li><a href="http://<?php echo $base_url ?>/suite"><?php echo $lang["menu"] ?></a></li>
    <li><a href="http://<?php echo $base_url ?>/suite/users/settings.php"><?php echo $lang["settings"] ?></a></li>
    <li><a href="http://<?php echo $base_url ?>/suite/worktime/all.php"><?php echo $lang["own_worktime"] ?></a></li>
    <li><a href="http://<?php echo $base_url ?>/suite/notifications/all.php"><?php echo $lang["notifications"] ?></a></li>
    <li class="b"><a><?php echo $name; ?></a></li>
    <li class="b"><a href="http://<?php echo $base_url ?>/suite/actions/auth/logout.php"><?php echo $lang["logout"] ?></a></li>
    <?php
    if(@$user->is_Admin($user->get_user(@$_SESSION["username"]))){
        $v = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/VERSION");
        echo "<li><a href='http://{$base_url}/suite/admin/worktime/all.php'>{$lang["a_allworktime"]}</a></li>";
        echo "<li><a href='http://{$base_url}/suite/admin/users/edit.php'>{$lang["a_useredit"]}</a></li>";
        echo "<li><a href='http://{$base_url}/suite/admin/worktime/sick/all.php'>{$lang["a_sickness"]}</a></li>";
        echo "<li><a href='http://{$base_url}/suite/admin/worktime/vacation/all.php'>{$lang["a_vacation"]}</a></li>";
        echo "<li><a href='http://{$base_url}/suite/plugins/index.php' target='_blank'>{$lang["a_plugins"]}</a></li>";
        echo "<li class='b' style='color:red'>ADMIN ACCOUNT | {$v}</li>";
    }
    
    ?>
</ul>