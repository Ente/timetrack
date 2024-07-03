<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/class/plugins/loader.plugins.arbeit.inc.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/class/auth/plugins/mail_password_reset.auth.arbeit.inc.php";
require_once dirname(__DIR__, 1) . "/src/Main.php";

use Userdetail\Userdetail;
use Arbeitszeit\Benutzer;
use Arbeitszeit\Auth;

$benutzer = new Benutzer;
$auth = new Auth;
$main = new Userdetail;

$nav = $main->compute_user_nav();
$user = $benutzer->get_user($_GET["user"]);

if($_POST["id"]){
    $payload = [
        "id" => $_POST["id"],
        "username" => $_POST["username"],
        "notes" => $_POST["notes"],
        "position" => $_POST["position"],
        "employee-id" => $_POST["employee-id"],
        "department" => $_POST["department"]
    ];
    if($main->save_employee_data($payload)){
        echo "Successfully saved data for {$payload["username"]}<br>";
    }
    if($_POST["reset-password"] == true || $_POST["reset-password"] == "on"){
        if($auth->reset_password($_POST["username"])){
            echo "Successfully reset password for {$payload["username"]}";
        }
    }
}

if($r = $main->get_employee_data($_GET["user"])){
    $notes = $r["notes"] ?? "";
    $pos = $r["position"] ?? "";
    $eid = $r["employee-id"] ?? "";
    $department = $r["department"] ?? "";
}
?>

<?php echo $nav ?>

<h2><?php echo $user["username"];  ?> | Userdetail</h2>
<div class="box">
    <form action="/suite/plugins/index.php?pn=userdetail&p_view=views/user.php&user=<?php echo $user["username"];  ?>&id=<?php echo $user["id"]; ?>" method="POST">
        <label>Username: </label><input type="text" min="3" name="username" value="<?php echo $user["username"] ?>"><br>
        <label>Reset Password? </label><input type="checkbox" name="reset-password"><br>
        <label>Notes: </label><textarea name="notes"><?php echo $notes; ?></textarea><br>
        <label>Position: </label><input type="text" name="position" value="<?php echo $pos; ?>" placeholder="CEO">
        <input type="text" name="id" value="<?php echo $user["id"]; ?>" hidden>

        <h3>HR</h3>
        <label>Employee ID: </label><input type="text" name="employee-id" value="<?php echo $eid; ?>" placeholder="000001"><br>
        <label>Department: </label><input type="text" name="department" value="<?php echo $department ?>"><br>
        <button class="button" name="save-employee-data" type="submit">Submit</button>
    </form>
</div>
