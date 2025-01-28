# PluginBuilder

The class `pluginbuilder.plugin.arbeit.inc.php` is the main class for building and using plugins.

The following code is run once a day, to ensure the peristance of the plugins through `.tp1` (**WIP**) files - Currently, this is not run, if you wish your plugin to be persistant, you can run

```php
<?php

require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/class/plugins/loader.plugins.arbeit.inc.php";
require $_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/arbeit.inc.php";
use macmonDashboard\PluginBuilder;
use Arbeitszeit\Arbeitszeit;
$arbeit = new Arbeitszeit;
$pb = new PluginBuilder;


$ini = $arbeit->get_app_ini();

while(true){

    if($ini["plugins"]["plugins"] == "true" && isset($ini["plugins"]["path"]) == true){
        if($pb->initialize_plugins()){
            if($pb->check_persistance()){
                return 0;
            }
        }
    }



}


?>
```

## Details

The Class is quite complicated. It features all functions you have to use, while using and loading your plugin (loading done by TimeTrack).

Your Plugin is allowed 3 functions to be used while being processed by TimeTrack, this can be either on loading, disabling or enabling the plugin. If you create the plugin with the `PluginBuilder::create_skelleton($name)` function, these function can be found within the `Main.php`.

In order to work with the TimeTrack classes, you have to specify `permissions` within your `plugin.yml`. Read more in the `../Permissions.md`.
