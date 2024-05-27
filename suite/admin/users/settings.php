<?php

require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Auth;
use Arbeitszeit\Benutzer;
use Arbeitszeit\Exceptions;
@session_start();
$ar = new Arbeitszeit();
$auth = new Auth();
$user = new Benutzer();

$ini = $ar->get_app_ini();

if(!$user->is_admin($user->get_user($_SESSION["username"]))){
    die();
}


?>
<br>
<div class="box">
    <h2>administrative Settings</h2>

    <p>This menu allows you to update the settings</p>

    <form  method="POST" action="/suite/admin/actions/users/edit_settings.php">
        <label>Site name: </label><input type="text" name="app_name" placeholder="The name of this application" value="<?php echo $ini["general"]["app_name"];  ?>">
        <br>
        <label>Base URL: </label><input type="text" name="base_url" placeholder="sub.domain.tld:<port>" value="<?php echo $ini["general"]["base_url"];  ?>">
        <br>
        <button type="submit" name="submit">Submit</button>
    </form>


<?php

$log_contents = @file_get_contents(Exceptions::logrotate()) ?? "Error retrieving log file!";

echo <<< DAT
<div class="box">
    <h2> Log file </h2>
    <p>See the contents of the log file below...</p>

    <pre style="font-family:monospace;text-size:x-small;">{$log_contents}</pre>


DAT;

?>
</div>