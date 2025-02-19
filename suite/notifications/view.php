<?php
require dirname(__DIR__, 2) . "/api/v1/inc/arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
$arbeit = new Arbeitszeit;
$arbeit->auth()->login_validation();
$id = $_GET["id"];
$data = $arbeit->notifications()->get_notifications_entry($id);

if(isset($data["error"])){
    header("Location: /suite/?info=notification_not_found");
    exit;
}

$loc = $arbeit->i18n()->loadLanguage(null, "notifications/view");
$iid = htmlspecialchars($id);
?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $loc["title"] ?> | <?php echo $arbeit->get_app_ini()["general"]["app_name"]; ?></title>
        <link rel="stylesheet" type="text/css" href="../../assets/css/index.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>

    <body>
        <h2><?php echo $loc["h2"] ?> <?php echo @strftime("%d.%m.%Y", strtotime($data["datum"])); ?></h2>
        <div class="box">
            <p><b><?php echo $loc["label_location"] ?>:</b> <?php  echo htmlspecialchars($data["ort"]);  ?></p>
            <p><b><?php echo $loc["label_date"] ?>:</b> <?php echo $datum = @strftime("%d.%m.%Y", strtotime($data["datum"])); ?></p>
            <p><b><?php echo $loc["label_time"] ?>:</b> <?php echo htmlspecialchars($data["uhrzeit"]); ?></p>
            <p><b><?php echo $loc["label_note"] ?>:</b><br> <?php echo htmlspecialchars($data["notiz"]); ?></p>
        </div>

        <?php
        # Bug 13
        if($arbeit->benutzer()->is_admin($arbeit->benutzer()->get_user($_SESSION["username"]))){
            echo <<< DATA
            <a href="../admin/actions/notifications/delete.php?id={$iid}">{$loc["a_delete"]}</a> | <span style="color:red">{$loc["a_delete_note"]}</span>
            DATA;   
        }
        ?>
    </body>
</html>