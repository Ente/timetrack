<?php

declare(strict_types=1);

namespace Arbeitszeit{
    class PluginPermissions extends PluginBuilder{
        final public function register_permissions(){
            $perms = $this->read_permissions();
            $handle = fopen($_SERVER["DOCUMENT_ROOT"] . "/core/plugins/permissions.json", "a+");
            foreach($perms as $perm => $key){
                if($handle == false || file($_SERVER["DOCUMENT_ROOT"] . "/core/plugins/permissions.json") == false){
                    # either the file does not exist, is corrupt or invalid
                     $data = [
                        "name" => $perm,
                        "description" => $key["description"]
                    ];

                    $data_2 = [
                        "plugins" => $data
                    ];

                    $data_2 = json_encode($data_2);
                    if($data_2 == false){
                        return false;
                    }

                    fwrite($handle, $data_2);
                    fclose($handle);
                    return true;
                }
            }
            return false;
        }

        final public function read_permissions(){
            $yml = $this->read_plugin_configuration(get_class($this));
            $perms = $yml["plugins"][$this]["permissions"]["register"];
            return $perms;
        }

        final public function get_permission($name){
            $perms = $this->read_permissions();
            foreach($perms as $perm => $key){
                if($name == $perm){
                    return [
                        "name" => $perm,
                        "description" => $key["description"]
                    ];
                }
            }

            return false;
        }
    }
}



?>