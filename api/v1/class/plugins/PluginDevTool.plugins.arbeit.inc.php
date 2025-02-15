<?php

declare(strict_types=1);

namespace Arbeitszeit{

    use Phar;


    class PluginDevTool extends PluginBuilder{

        /**
         * Create a plugin file by easily creating a phar-archive
         * 
         * @param string $name The plugin name relative from the plugin path (e.g. example)
         * @param string $stub The file being used when using the phar archive from the cli
         */
        public function create_plugin($name, $stub){
            Exceptions::error_rep("[PluginDevTool] Creating plugin '$name'...");
            $plugins = $this->get_plugins();
            foreach($plugins["plugins"] as $plugin => $keys){
                if($plugin == $name){
                    $p = new PluginBuilder;
                    $phar = new Phar($_SERVER["DOCUMENT_ROOT"] . $p->get_basepath() . "/" . $name . ".phar");
                    $phar->buildFromDirectory($_SERVER["DOCUMENT_ROOT"] . $p->get_basepath() . "/" . $name);
                    $phar->setDefaultStub($stub);
                    $this->logger(parent::$la . " Plugin '$name' has been created successfully!");
                    return $_SERVER["DOCUMENT_ROOT"] . $p->get_basepath() . "/". $name;
                } else {
                    $this->logger(parent::$la . " Plugin files for '$name' could not be found. Perhaps misspelled the name?");
                    return false;
                }

            }
            return false;
        }

        public function extract_plugin($name){
            $this->logger(parent::$la . " Extracting plugin '$name'...");
            $plugins = $this->get_plugins();
            if(file_exists($name)){
                
            }
        }
    }
}



?>