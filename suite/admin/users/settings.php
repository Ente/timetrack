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
<div class="box">
    <h2> {$language["log_title"]} </h2>
    <p>{$language["log_p1"]}</p>

    <pre style="font-family:monospace;text-size:x-small;">{$log_contents}</pre>
DAT;
?>
</div>