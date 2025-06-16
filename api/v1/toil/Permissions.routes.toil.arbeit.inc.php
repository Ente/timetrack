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
            Exceptions::error_rep("[API] Loading permissions...");
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

    
        public function checkPermissions($user, $endpoint) {
            $permissionsSet = $this->__get("permissions");
            $endpointPermission = $permissionsSet[$endpoint] ?? null;
        
            if ($endpointPermission === null) {
                Exceptions::error_rep("[API] No permissions defined for endpoint: '$endpoint'");
                return false;
            }
        
            if ($endpointPermission === 2) {
                Exceptions::error_rep("[API] Public access granted for endpoint '$endpoint'");
                return true;
            }
            $userIsAdmin = $this->checkUserPermission($user);
        
            if ($endpointPermission === 1 && $userIsAdmin) {
                return true;
            }
        
            if ($endpointPermission === 0) {
                return true;
            }
        
            Exceptions::error_rep("[API] Permission denied for user '$user' on endpoint '$endpoint' (required: $endpointPermission, isAdmin: $userIsAdmin)");
            return false;
        }
        
        
    }
}

?>