<?php
ini_set("display_errors", 1);
require_once "../api/v1/class/arbeitszeit.inc.php";
use Arbeitszeit\Arbeitszeit;

$ar = new Arbeitszeit();
# version check

$version = file_get_contents("../VERSION");

if($version == false || $version == false){
    echo "Version validation failed. Your version could either not be read or is above 3.0 | Version: {$version}";
    return 1;
}


# first get passwords and hash them to write them back again :)

$sql = "SELECT * FROM users";
$res = mysqli_query($ar->get_conn(), $sql);

if(mysqli_error($ar->get_conn())){
    return "MYSQLi ERROR: " . mysqli_error($ar->get_conn());
}

while($row = mysqli_fetch_assoc($res)){
    if(strpos("$2y$", $row["password"])){
        echo "Account '{$row["username"]}' already hashed";
        return 2;
    }

    $old = $row["password"];
    $new = password_hash($old, PASSWORD_DEFAULT);
    $sql = "UPDATE `users` SET `password` = '{$new}' WHERE id = {$row["id"]}";
    $query = mysqli_query($ar->get_conn(), $sql);

    if($query == true){
        echo "Hashed password for account '{$row["username"]}'";
    } else {
        echo "Failed to hash password for account '{$row["username"]}'";
        return 1;
    }

}
return 0;

?>