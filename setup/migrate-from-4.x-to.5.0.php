<?php
ini_set("display_errors", 1);
require_once "../api/v1/class/arbeitszeit.inc.php";
use Arbeitszeit\Arbeitszeit;

$ar = new Arbeitszeit;
# version check

$version = file_get_contents("../VERSION");

if($version == false || $version == false){
    echo "Version validation failed. Your version could either not be read or is above 5.0 | Version: {$version}";
    return 1;
}

# debug, print out app.ini

try {
    echo "<pre>" . var_dump($ar->get_app_ini()) . "</pre>";
} catch (Exception $e){
    echo $e->getMessage();
}

# check for table "schicht" to support new mode for stationary devices

$sql_tablecheck = "SELECT TABLES LIKE 'schicht'";
$sql_createtable = "CREATE TABLE 'schicht' ( `id` int(11) NOT NULL, `name` varchar(256) NOT NULL, `email` varchar(256) NOT NULL, `schicht_gestartet_zeit` varchar(256) DEFAULT NULL, `schicht_ende_zeit` varchar(256) DEFAULT NULL, `schicht_datum` varchar(256) DEFAULT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
$sql_usertableaddeasymode = "ALTER TABLE `users` ADD `easymode` BOOLEAN NULL AFTER `state`;";
$sql_arbeitszeitentableaddreview = "ALTER TABLE `arbeitszeiten` ADD `review` BOOLEAN NULL AFTER `ort`;";
$sql_arbeitszeitentableaddactive = "ALTER TABLE `arbeitszeiten` ADD `active` BOOLEAN NULL AFTER `ort`;";

$res_tablecheck = mysqli_query(Arbeitszeit::get_conn(), $sql_tablecheck);
$res_usertableaddeasymode = mysqli_query(Arbeitszeit::get_conn(), $sql_usertableaddeasymode);
$res_arbeitszeitentableaddreview = mysqli_query($ar->get_conn(), $sql_arbeitszeitentableaddreview);

if(mysqli_error(Arbeitszeit::get_conn())){
    if(mysqli_num_rows($res_tablecheck) == 1){
        echo "Table already exists, not checking for structure. Moving to the next task...";
    } else {
        $res_createtable = mysqli_query(Arbeitszeit::get_conn(), $sql_createtable);
        if($res_createtable == true){
            echo "Table has been successfully created. Moving to the next task...";
        }
    }

    if($res_usertableaddeasymode){
        echo "Successfully altered table 'users' for easymode bool. Moving to the next task...";
    } else {
        echo "Failed to alter table 'users' for easymode bool, may already exist.. Moving to the next task...";
    }

    if($res_arbeitszeitentableaddreview){
        echo "Successfully altered table 'arbeitszeiten' for active bool. Moving to the next task...";
    } else {
        echo "Failed to alter table 'arbeitszeiten' for active bool, may already exist.. Moving to the next task...";
    }

    if($res_arbeitszeitentableaddactive){
        echo "Successfully altered table 'users' for easymode bool. Moving to the next task...";
    } else {
        echo "Failed to alter table 'users' for easymode bool, may already exist.. Moving to the next task...";
    }

    if($res_usertableaddeasymode){
        echo "Successfully altered table 'users' for easymode bool. Moving to the next task...";
    } else {
        echo "Failed to alter table 'users' for easymode bool, may already exist.. Moving to the next task...";
    }
} else {
    echo "An error occured while connecting to the database. SQL-Error:" . mysqli_error($ar->get_conn());
    return 1;
}

# check and add the new "auto_update" key to false in app.ini

if(!isset($ar->get_app_ini()["general"]["mode"]) && !isset($ar->get_app_ini()["general"]["auto_update"])){
    $ini = $ar->get_app_ini();
    $ini["general"]["auto_update"] = false;

    $new = $ar->arr2ini($ini);

    # write to conf file

    $handle = fopen("../api/v1/inc/app.ini", "w");
    if(fwrite($handle, $new)){
        echo "Successfully added 'auto_update' and 'mode' to 'general' section of app.ini! Moving to the next task...";
    } else {
        echo "An error occured while writing to file, aborting...";
        return 1;
    }

} else {
    echo "Skipping auto_update key";
}

echo "Finished migration to 5.0! Please check your instance now.";
?>