<?php
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
$username = $_SESSION["username"];
$arbeit = new Arbeitszeit;
$ini = $arbeit->get_app_ini();
$base_url = $ini["general"]["base_url"];
$language = $arbeit->i18n()->loadLanguage(null, "notifications/edit", "admin");

$arbeit->auth()->login_validation();
if(!@$arbeit->benutzer()->is_admin($arbeit->benutzer()->get_user($username))){
    header("Location: http://{$base_url}/suite/?" . $arbeit->statusMessages()->URIBuilder("noperms"));
}
$id = htmlspecialchars($_GET["id"]);
$data = $arbeit->notifications()->get_notifications_entry($id);
?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $language["title"] ?> | <?php echo $ini["general"]["app_name"]; ?></title>
        <link rel="stylesheet" type="text/css" href="/assets/css/index.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/gui/standard_nav.php" ?>

        <h1><?php echo $language["title"] ?></h1>
        <div class="box">
            <form action="/suite/admin/actions/notifications/edit.php?id=<?php echo $id; ?>" method="POST">
                <label><?php echo $language["label_date"] ?>: </label><input class="input" type="date" name="datum" value="<?php echo htmlspecialchars($data["datum"]) ?>">
                <br><hr>
                <label><?php echo $language["label_time"] ?>: </label><input class="input" type="time" name="uhrzeit" value="<?php echo htmlspecialchars($data["uhrzeit"]); ?>">
                <br><hr>
                <label><?php echo $language["label_location"] ?>: </label><input class="input" type="text" name="ort" value="<?php echo htmlspecialchars($data["ort"]); ?>">
                <br><hr>
                <label><?php echo $language["label_note"] ?>: </label><input class="input" type="text" name="notiz" value="<?php echo htmlspecialchars($data["notiz"]);  ?>">
                <br><hr>
                <button type="submit" name="submit" class="button"><?php echo $language["submit_text"] ?></button>
            </form>
        </div>
    </body>
</html>