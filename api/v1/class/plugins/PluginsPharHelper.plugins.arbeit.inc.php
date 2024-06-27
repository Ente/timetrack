<?php
declare(strict_types=1);
/**
 * This class shouold not be used, yet
 */
namespace Arbeitszeit{
    class PluginPharHelper extends PluginBuilder{
        public function get_plugins_phar(): array{ # bullshit function
            $dir = array_diff(scandir($_SERVER["DOCUMENT_ROOT"] . $this->basepath), array(".", ".."));
                foreach($dir as $plugin_p){
                    $phar = new \Phar($_SERVER["DOCUMENT_ROOT"] . $this->basepath . "/" . $plugin_p);
                    if($phar instanceof \Phar == true){
                        return [];
                    } else {
                        throw new \UnexpectedValueException("An error occured, while reading phar archive '{$plugin_p}'!");
                    }
                }
        }
    }
}





?>