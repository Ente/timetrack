<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Exceptions;
@session_start();
$ar = new Arbeitszeit();
$language = $arbeit->i18n()->loadLanguage(null, "users/settings", "admin");
$ini = $ar->get_app_ini();
if(!$arbeit->benutzer()->is_admin($arbeit->benutzer()->get_user($_SESSION["username"]))){
    header("Location: /suite/");
}
?>
<br>
<div class="box">
<?php
$log_contents = @Exceptions::getLastLines(Exceptions::getSpecificLogFilePath(), 200)?? "Error retrieving log file!";
echo <<< DAT
<div class="card v8-bordered log-box">
    <h2>{$language["log_title"]}</h2>
    <p>{$language["log_p1"]}</p>

    <pre class="log-output">{$log_contents}</pre>
</div>

DAT;

?><br><br>
<?= $arbeit->renderGUIUpdateCheck(); ?><br><br>
<div class="card v8-bordered log-box">
    <h2>Telemetry</h2>
    <p>Here you can send anonymous telemetry data to help improve the application.
        What data will be sent? Total user count, total API calls, version information, and general system information (PHP version, database type, etc.).
        <?php if(($ini["general"]["telemetry"] ?? "enabled") === "enabled"){
            echo "Telemetry is currently <strong>enabled</strong>.";
        } else {
            echo "Telemetry is currently <strong>disabled</strong>. This setting can only be changed by editing the configuration file manually.";
        } ?>
    </p>

    <?php if(($ini["general"]["telemetry"] ?? "enabled") === "enabled"): ?>
        <form method="post" action="/suite/admin/actions/users/telemetry.php">
            <label for="telemetry_send_button">Send telemetry data now: </label>
            <input type="checkbox" id="telemetry_send_button" name="telemetry_send_button" value="send"><br><br>
            <button type="submit">Send now.</button>
        </form>
    <?php else: ?>
        <p><em>Telemetry is disabled. To send telemetry data, please enable it in the configuration file.</em></p>
    <?php endif; ?>
</div>