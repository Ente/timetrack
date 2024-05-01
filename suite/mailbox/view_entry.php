<?php
require dirname(__DIR__, 2) . "/api/v1/inc/arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Kalender;
use Arbeitszeit\Benutzer;
use Arbeitszeit\Auth;
use Arbeitszeit\Mailbox;
$username = $_SESSION["username"];
$auth = new Auth;
$calendar = new Kalender;
$user = new Benutzer;
@$mb = new Mailbox;
$arbeit = new Arbeitszeit;
$base_url = $ini = Arbeitszeit::get_app_ini();
$auth->login_validation();

# validate id and attachement
if(!isset($_GET["id"], $_GET["attachement-id"])){
    $r = "Error:  ";
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Mailbox | <?php echo $ini["general"]["app_name"]; ?></title>
        <link rel="stylesheet" type="text/css" href="../../../assets/css/index.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <?php include "../../assets/gui/standard_nav.php"; ?>
        <h1>Nachricht | <?php echo $ini["general"]["app_name"]; ?></h1>
        <div class="box">
            <?php if(isset($r)){
                echo $r;
            } else {
                $letter_data = $mb->get_user_mailbox_entry_f($_GET["id"], $_GET["attachement-id"]);
                echo $letter_data["letter"];
                echo $letter_data["attachement"];
            }   ?>
        </div>
    </body>
</html>