<?php
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
use Arbeitszeit\Arbeitszeit;
$worktime = new Arbeitszeit;
$base_url = $worktime->get_app_ini()["general"]["base_url"];
$worktime->auth()->login_validation();
$id = $_POST["worktime_id"];
$work = $worktime->update_worktime($id, ["schicht_anfang" => $_POST["new_start"], "schicht_ende" => $_POST["new_end"], "ort" => $_POST["new_comment"] . " - SYS: " . $_POST["reason"]]);
if(!$work){
    header("Location: http://{$base_url}/suite/?" . $worktime->statusMessages()->URIBuilder('error_worktime_update'));
} else {
    header("Location: http://{$base_url}/suite/?" . $worktime->statusMessages()->URIBuilder('worktime_updated'));
}



?>