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
<?php
// reset_password.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Passwort zurücksetzen | <?= $ini["general"]["app_name"]; ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=JetBrains+Mono&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/v8.css?v=1">
</head>
<body>
  <div class="animated-bg"></div>

  <main style="display:flex;justify-content:center;align-items:center;min-height:100vh;flex-direction:column;">
    <div class="card" style="max-width: 420px; width: 100%;">
      <h2 class="text-center">🔐 <?= $language["pw_reset_title"] ?? "Reset your password" ?></h2>
      <p><?= $language["pw_reset_note"] ?? "Please enter your new password below." ?></p>

      <form method="POST" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <input class="input" type="password" name="password" placeholder="<?= $language["pw_reset_placeholder"] ?? "New password"; ?>" required>
        <input type="hidden" name="auth" value="<?= htmlspecialchars(explode(";", base64_decode($_GET["token"]))[1]); ?>">
        <button type="submit" name="reset" value="true"><?= $language["pw_reset_submit"] ?? "Submit" ?></button>
      </form>
    </div>
  </main>

  <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/gui/standard_footer.php"; ?>
</body>
</html>
