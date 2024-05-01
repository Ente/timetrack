<?php
require_once dirname(__DIR__, 2) . "/api/v1/inc/arbeit.inc.php";
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Benutzer;

$arbeit = new Arbeitszeit;
$user = new Benutzer;
$ini = $arbeit->get_app_ini();
$base_url = $ini["general"]["base_url"];
$name = $ini["general"]["app_name"];
?>
<script type="text/javascript" id="cookiebanner"
  src="https://cdnjs.cloudflare.com/ajax/libs/cookie-banner/1.2.2/cookiebanner.min.js"></script>
<ul>
    <li><a href="http://<?php echo $base_url ?>/suite">Menü</a></li>
    <li><a href="http://<?php echo $base_url ?>/suite/users/settings.php">Einstellungen</a></li>
    <li><a href="http://<?php echo $base_url ?>/suite/worktime/all.php">Eigene Arbeitszeiten</a></li>
    <li><a href="http://<?php echo $base_url ?>/suite/mailbox/all.php">Eigene Mailbox</a></li>
    <li><a href="http://<?php echo $base_url ?>/suite/calendar/all.php">Kalender</a></li>
    <li class="b"><a><?php echo $name; ?></a></li>
    <li class="b"><a href="http://<?php echo $base_url ?>/suite/actions/auth/logout.php">Abmelden</a></li>
    <?php
    if(@$user->is_Admin($user->get_user(@$_SESSION["username"]))){
        $v = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/VERSION");
        echo "<li><a href='http://{$base_url}/suite/admin/worktime/all.php'>Alle Arbeitszeiten</a></li>";
        echo "<li><a href='http://{$base_url}/suite/admin/users/edit.php'>Benutzer bearbeiten</a></li>";
        echo "<li><a href='http://{$base_url}/suite/admin/mailbox/ov.php'>Mailbox-Admin</a></li>";
        echo "<li><a href='http://{$base_url}/suite/admin/worktime/sick/all.php'>Alle Krankheiten</a></li>";
        echo "<li><a href='http://{$base_url}/suite/admin/worktime/vacation/all.php'>Alle Urlaube</a></li>";
        echo "<li class='b' style='color:red'>ADMIN ACCOUNT | {$v}</li>";
    }
    
    ?>
</ul>