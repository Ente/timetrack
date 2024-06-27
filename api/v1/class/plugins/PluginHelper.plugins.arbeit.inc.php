<?php

namespace Arbeitszeit{
    class PluginHelper extends PluginBuilder{
        public static function read_configuration($basepath){
            return $conf = \yaml_parse_file($basepath . "/" . "plugin.yml");
        }
    }
}



?>