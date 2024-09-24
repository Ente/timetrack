<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/api/v1/class/plugins/loader.plugins.arbeit.inc.php";
use Arbeitszeit\PluginBuilder;
use Arbeitszeit\Arbeitszeit;
$arbeit = new Arbeitszeit;
$arbeit->auth()->login_validation();
$pl = new PluginBuilder();
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="/assets/css/index.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <?php
            include "nav.php";
        ?>
        <hr>
        <?php

            if(!isset($_GET["pn"])){
                echo "No plugin selected.";
                goto e;
            }

            $f = $pl->load_plugin_view($_GET["pn"], $_GET["p_view"]);
            if($f == false){
              echo "<p>An error occured while getting the requested view! Probably it simply does not exist or could not be imported. More information can be found within the log.</p>";
              print_r($_GET);
            }

            e:
        ?>
    </body>
</html>