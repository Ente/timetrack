<?php
use Arbeitszeit\Mailbox;
require dirname(__DIR__, 4) . "/api/v1/inc/arbeit.inc.php";
session_start();
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Kalender;
use Arbeitszeit\Benutzer;
use Arbeitszeit\Auth;
$username = $_SESSION["username"];
$auth = new Auth;
$calendar = new Kalender;
$user = new Benutzer;
$arbeit = new Arbeitszeit;
$base_url = $ini = Arbeitszeit::get_app_ini()["general"]["base_url"];

$mb = new Mailbox;


$auth->login_validation();
if($user->is_admin($user->get_user($_SESSION["username"]))){
    $id = $mb->create_mailbox_entry($_POST["name"], $_POST["description"], $_POST["user"]);
    if($id == false){
        if(isset($_POST["file"])){
            if($_POST["file"] == "Link"){header("Location: http://{$base_url}/suite/?info=mailbox_entry_created"); }
            if($mb->create_mailbox_file_entry(["url" => $_POST["file"], "pw" => $_POST["file_password"]], $id) == true){
                header("Location: http://{$base_url}/suite/?info=mailbox_entry_created");
            } else {
                header("Location: http://{$base_url}/suite/?info=mailbox_file_entry_failed");
            }
        } else {
            header("Location: http://{$base_url}/suite/?info=mailbox_entry_created");
        }
    }  else {
        header("Location: http://{$base_url}/suite/?info=mailbox_entry_failed");
    }
} else {
    header("Location http://{$base_url}/suite/?info=noperms");
}


?>