<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/class/plugins/loader.plugins.arbeit.inc.php";
require_once dirname(__DIR__, 1) . "/src/Main.php";

use Arbeitszeit\Arbeitszeit;
use utility\utility;
use Arbeitszeit\Benutzer;

$a = new Arbeitszeit;
$benutzer = new Benutzer;
$main = new utility;

$a->auth()->login_validation();
$a->benutzer()->current_user_is_admin();
?>

<h2>User Export</h2>
<div class="box">
    <p>Please input the username of the user you want to export all data of below.</p>
    <p>You can find the list here: <a href="/suite/admin/users/edit.php#userlist" target="_blank">Edit Users</a>.</p>
    <form action="/api/v1/class/plugins/plugins/utility/views/download.php" method="POST">
        <label>Username: </label>
        <input type="text" name="username" required>
        <button class="button" type="submit">Download JSON export</button>
        <p>After clicking on "Download JSON export" your browser will open a dialog to save the generated export file.</p>
    </form>
</div>
