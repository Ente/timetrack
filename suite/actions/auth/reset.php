<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";

use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Auth;
use Arbeitszeit\Exceptions;
use Arbeitszeit\DB;
use Arbeitszeit\Mails\Provider\PHPMailerMailsProvider;

$arbeitszeit = new Arbeitszeit;
$auth = new Auth;

$ini = $arbeitszeit->get_app_ini();

if(isset($_POST["email"])){
    $arbeitszeit->mails()->init(new PHPMailerMailsProvider($arbeitszeit, $arbeitszeit->benutzer()->get_username_from_email($_POST["email"])));
    $arbeitszeit->mails()->sendMail("PasswordResetTemplate", ["username" => $arbeitszeit->benutzer()->get_username_from_email($_POST["email"]), "email" => $_POST["email"]]);
    header("Location: /suite/login.php?" . $arbeitszeit->statusMessages()->URIBuilder("password_reset"));
}

if(isset($_POST["password"]) == true && isset($_POST["auth"]) == true){
    $db = new DB;
    $sql = "SELECT * FROM `users` WHERE email = ?";
    $query = $db->sendQuery($sql);
    $query->execute([$_POST["auth"]]);
    @$count = $query->rowCount();
    if($count != 0 || $count >! 1){
        $data = $query->fetch(\PDO::FETCH_ASSOC);
        $pass = password_hash($_POST["password"], PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = ? WHERE email = ?;";
        $res = $db->sendQuery($sql)->execute([$pass, $_POST["auth"]]);
        if($res){
            $arbeitszeit->mails()->init(new PHPMailerMailsProvider($arbeitszeit, $data["username"]));
            $arbeitszeit->mails()->sendMail("PasswordChangedTemplate", ["username" => $data["username"], "email" => $_POST["auth"]]);
            header("Location: /suite/login.php?" . $arbeitszeit->statusMessages()->URIBuilder("password_changed"));
        } else {
            Exceptions::error_rep("Could not change password as the query failed!. See previous message for more information.");
            header("Location: /suite/login.php?". $arbeitszeit->statusMessages()->URIBuilder("password_change_failed"));
        }
    } else {
        Exceptions::error_rep("Could not reset password as the user could not be found! | Email: " . $_POST["auth"]);
        echo "Could not find user!";
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Passwort zurücksetzen - Neues Passwort festlegen | <?php echo $ini["general"]["app_name"]; ?></title>
        <link rel="stylesheet" type="text/css" href="../../../assets/css/index.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>

        <form class="box" action="<?php $_SERVER["PHP_SELF"]; ?>" method="POST">
            <h2>Password forgot?</h2>
            <p>Please enter your new password in the below's form.</p>
            <p>You will recieve a email after submiting your new password.</p>
            <label>Your new password: </label><input class="input" type="password" name="password" placeholder="Password">
            <input type="text" name="auth" value="<?php echo htmlspecialchars(explode(";", base64_decode($_GET["token"]))[1]);  ?>" hidden>
            <br>
            <button class="input" type="submit" name="reset" value="true">Submit</button>
        </form>
    </body>
</html>