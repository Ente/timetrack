<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Exceptions;
@session_start();
$ar = new Arbeitszeit();
$language = $arbeit->i18n()->loadLanguage(null, "users/settings", "admin");
$ini = $ar->get_app_ini();
if(!$arbeit->benutzer()->is_admin($arbeit->benutzer()->get_user($_SESSION["username"]))){
    die();
}
?>
<br>
<div class="box">
    <h2><?php echo $language["qtitle"] ?></h2>

    <p><?php echo $language["p1"] ?></p>

    <form  method="POST" action="/suite/admin/actions/users/edit_settings.php">
        <label><?php echo $language["label_sitename"] ?>: </label><input type="text" name="app_name" placeholder="<?php echo $language["placeholder_sitename"] ?>" value="<?php echo $ini["general"]["app_name"];  ?>">
        <br>
        <label><?php echo $language["label_base_url"] ?>: </label><input type="text" name="base_url" placeholder="<?php echo $language["placeholder_base_url"] ?>" value="<?php echo $ini["general"]["base_url"];  ?>">
        <br>
        <button type="submit" name="submit"><?php echo $language["button_text"] ?></button>
    </form>
<?php
$log_contents = @file_get_contents(Exceptions::logrotate()) ?? "Error retrieving log file!";
echo <<< DAT
<div class="box">
    <h2> {$language["log_title"]} </h2>
    <p>{$language["log_p1"]}</p>

    <pre style="font-family:monospace;text-size:x-small;">{$log_contents}</pre>
DAT;
?>
</div>