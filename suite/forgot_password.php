<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/class/auth/plugins/mail_password_reset.auth.arbeit.inc.php";
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Auth;
use Arbeitszeit\MailPasswordReset;

$arbeitszeit = new Arbeitszeit;
$auth = new Auth;
$reset = new MailPasswordReset;

$ini = $arbeitszeit->get_app_ini();

if(@$_POST["reset"] == true && @isset($_POST["email"])){
    $conn = Arbeitszeit::get_conn();
    $sql = "SELECT * FROM users WHERE email = '{$_POST["email"]}'";
    $query = mysqli_query($conn, $sql);
    @$count = mysqli_num_rows($query);
    if($count == 0 || $count > 1){
        echo "The user does not exist, please re-check the input values!";
    } else {
        $id = mysqli_fetch_assoc($query)["username"];
        if($auth->reset_password($id) == true){
            echo "An email to reset your password has been sent to your email address.";
        } else {
            echo "Could not send an email to your account. Please contact the system administrator!";
        }
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Passwort zurücksetzen | <?php echo $ini["general"]["app_name"]; ?></title>
        <link rel="stylesheet" type="text/css" href="/assets/css/index.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>

        <form class="box" action="<?php $_SERVER["PHP_SELF"]; ?>" method="POST">
            <h2>Password forgot?</h2>
            <p>Please enter your e-mail in the below's form.</p>
            <label>Your E-Mail: </label><input type="email" name="email" placeholder="you@mail.com">
            <br>
            <button type="submit" name="reset" value="true">Submit</button>
        </form>
    </body>
</html>