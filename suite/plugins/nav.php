<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/class/plugins/loader.plugins.arbeit.inc.php";
use Arbeitszeit\PluginBuilder;
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Auth;
$arbeit = new Arbeitszeit;
$auth = new Auth;
$auth->login_validation();
$ini = Arbeitszeit::get_app_ini();
$name = $ini["general"]["app_name"];
$pl = new PluginBuilder(Arbeitszeit::get_app_ini()["plugins"]);
?>

<ul>
    <?php

        $plugins = $pl->get_plugins();
        echo "<h1>Plugin Hub | {$name} </h1>";
        
        if(!isset($plugins)){
            $PC_true = true;
        }
        
        foreach($plugins["plugins"] as $plugin){
            echo $pl->get_plugin_nav_html($plugin["name"]);
        }


    ?>
</ul>