<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/class/plugins/loader.plugins.arbeit.inc.php";
use Arbeitszeit\PluginBuilder;
use Arbeitszeit\Arbeitszeit;
$arbeit = new Arbeitszeit;
$arbeit->auth()->login_validation();
$ini = $arbeit->get_app_ini();
$name = $ini["general"]["app_name"];
$pl = new PluginBuilder();
?>
<ul>
    <?php
        $plugins = $pl->get_plugins();
        echo "<h1>Plugin Hub | {$name} </h1>";
        
        if(!isset($plugins)){
            $PC_true = true;
        }
        
        foreach($plugins["plugins"] as $plugin){
            echo @$pl->get_plugin_nav_html($plugin["name"]);
        }
    ?>
</ul>