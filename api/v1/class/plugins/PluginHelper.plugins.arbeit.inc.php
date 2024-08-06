<?php

namespace Arbeitszeit{
    /**
     * @deprecated This class is deprecated and will be removed in future releases.
     */
    class PluginHelper extends PluginBuilder{
        public static function read_configuration($basepath){
            return $conf = \yaml_parse_file($basepath . "/" . "plugin.yml");
        }
    }
}



?>