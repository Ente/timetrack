<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/class/plugins/loader.plugins.arbeit.inc.php";
require_once dirname(__DIR__, 1) . "/src/Main.php";

use Userdetail\Userdetail;
use Arbeitszeit\Benutzer;
use Arbeitszeit\Auth;

$benutzer = new Benutzer;
$auth = new Auth;
$main = new Userdetail;

$nav = $main->compute_user_nav();
$user = $benutzer->get_user($_GET["user"]);

$id = filter_var($_POST["id"], FILTER_SANITIZE_NUMBER_INT);

if(isset($_GET["nuser"])){
    echo "<p>User not found.</p>";
    die();
}

if($id){
    $payload = [
        "id" => $id,
        "username" => $_POST["username"],
        "notes" => $_POST["notes"],
        "position" => $_POST["position"],
        "employee-id" => $_POST["employee-id"],
        "department" => $_POST["department"],
        "email" => $_POST["email"],
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
    <form id="userForm" action="/suite/plugins/index.php?pn=userdetail&p_view=views/user.php&user=<?php echo $user["username"];  ?>&id=<?php echo $user["id"]; ?>" method="POST">
        <label>Username: </label><input type="text" min="3" name="username" value="<?php echo $user["username"]; ?>"><br>
        <label>Reset Password? </label><input type="checkbox" name="reset-password"><br>
        <label>Notes: </label><textarea name="notes"><?php echo htmlspecialchars($notes); ?></textarea><br>
        <label>Position: </label><input type="text" name="position" value="<?php echo htmlspecialchars($pos); ?>" placeholder="CEO"><br>
        <label>Email:</label><input type="email" name="email" value="<?php echo htmlspecialchars($user["email"]); ?>" placeholder="box@mail.com"><br>
        <input type="text" name="id" value="<?php echo htmlspecialchars($user["id"]); ?>" hidden>

        <h3>HR</h3>
        <label>Employee ID: </label><input type="text" name="employee-id" value="<?php echo htmlspecialchars($eid); ?>" placeholder="000001"><br>
        <label>Department: </label><input type="text" name="department" value="<?php echo htmlspecialchars($department) ?>"><br>
        <button class="button" name="save-employee-data" type="submit">Submit</button>
    </form>
</div>

<script>
    document.getElementById("userForm").addEventListener("submit", function(event){
        event.preventDefault();

        let confirmation = confirm("If you have changed the username, please be aware that this can cause unexpected consequences. Are you sure you want to continue?");
        if(confirmation){
            this.submit();
        }
    })
</script>
