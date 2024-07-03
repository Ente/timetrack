<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/class/auth/plugins/mail_password_reset.auth.arbeit.inc.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/class/auth/plugins/mail_password_changed.auth.arbeit.inc.php";

use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Auth;
use Arbeitszeit\MailPasswordReset;
use Arbeitszeit\MailPasswordChanged;
use Arbeitszeit\Exceptions;

$arbeitszeit = new Arbeitszeit;
$auth = new Auth;
$reset = new MailPasswordReset;
$changed = new MailPasswordChanged;

$ini = $arbeitszeit->get_app_ini();

if(isset($_POST["password"]) == true && isset($_POST["auth"]) == true){
    $conn = Arbeitszeit::get_conn();
    $sql = "SELECT * FROM `users` WHERE email = '{$_POST["auth"]}'";
    $query = mysqli_query($conn, $sql);
    @$count = mysqli_num_rows($query);
    if($count != 0 || $count >! 1){
        $data = mysqli_fetch_assoc($query);
        $pass = password_hash($_POST["password"], PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = '{$pass}' WHERE email = '{$_POST["auth"]}';";
        $res = mysqli_query($conn, $sql);
        if($res != false){
            $changed->mail_password_changed($data["username"], $auth->mail_init($data["username"], true));
            header("Location: /suite/index.php?info=password_changed");
        } else {
            Exceptions::error_rep("Could not change password as the query failed! | MySQLI error: " . mysqli_error($conn));
            header("Location: /suite/index.php?info=password_change_failed");
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
            <label>Your new password: </label><input type="password" name="password" placeholder="Password">
            <input type="text" name="auth" value="<?php echo explode(";", base64_decode($_GET["token"]))[1];  ?>" hidden>
            <br>
            <button type="submit" name="reset" value="true">Submit</button>
        </form>
    </body>
</html>