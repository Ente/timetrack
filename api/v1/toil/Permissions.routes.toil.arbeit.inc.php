<?php
namespace Toil{
    use Arbeitszeit\Arbeitszeit;
    use Arbeitszeit\Exceptions;

    /**
     * Permissions Class
     * 
     * This class helps the Routes class to interpret permissions for each API endpoint.
     * It's pretty simple as this class loads a configuration file, containing the correct permissions for each endpoint. depending on if the user is admin or not, access will be granted or not.
     */
    class Permissions extends Routes {

        private Arbeitszeit $arbeitszeit;

        private array $permissions;

        public function __construct(){
            $this->__set("arbeitszeit", new Arbeitszeit());
            $this->__set("permissions", $this->loadPermissionSet());
        }

        public function __set($name, $value){
            $this->$name = $value;
        }

        public function __get($name){
            return $this->$name ?? null;
        }

        private function loadPermissionSet(){
            try {
                return json_decode(file_get_contents(dirname(__FILE__, 1) . "/permissions.json"), true);
            } catch(\Exception $e){
                Exceptions::failure($e->getCode(), $e->getMessage(), $e->getTraceAsString());
            }
        }

        private function checkUserPermission($username){
            $user = $this->arbeitszeit->benutzer()->get_user($username);
            return $user["isAdmin"];
        }

    
        public function checkPermissions($user, $endpoint){
            $endpointP = @$this->loadPermissionSet()[$endpoint];
            if($endpointP === null) {Exceptions::error_rep("[API] Failed to check for permissions on endpoint: " . $endpoint . " (req: ". $endpointP . ", for user " . $user); return false;};

            $perm = $this->checkUserPermission($user);
            switch ($perm) {
                case 1:
                case $endpointP === 0:
                    return true;
                case $perm == $endpointP:
                    return true;
                default:
                    return false;
            }
        }
        
    }
}

?>