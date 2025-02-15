<?php
namespace Arbeitszeit{

    use TypeError;

    /**
     * This class works as updater aswell as migrating tool
     */
    class Updates {
        private int $scheme;

        public function __get($name){
            return $this->$name;
        }

        public function __set($name, $value){
            $this->$name = $value;
        }

        private function request_schemev(){
            $conn = new DB;
            Exceptions::error_rep("Checking database scheme version...");
            try {
                $sql = "SELECT * FROM scheme;";
                $res = $conn->sendQuery($sql);
                $res->execute();
                Exceptions::error_rep("Successfully checked database scheme version.");
                $data = $res->fetch(\PDO::FETCH_ASSOC)["v"];
            } catch(\Exception $e){
                Exceptions::error_rep("Database scheme not found. Creating new scheme...");
                $sql = "CREATE TABLE scheme (v INT DEFAULT 1);";
                $sql1 = "INSERT INTO scheme (v) VALUES (1);";
                $conn->simpleQuery($sql);
                $conn->simpleQuery($sql1);
                Exceptions::error_rep("Successfully created new database scheme. Retrying to get scheme version...");
                $this->request_schemev();
            }

            return $data;
        }

        private function get_latest_schemev(){
            Exceptions::error_rep("Getting latest database scheme version...");
            $dir = dirname(__DIR__) . "/updates/data/scheme.db";
            $d = file_get_contents($dir);
            return $d;
        }

        public function compare_scheme(){
            Exceptions::error_rep("Comparing database scheme versions...");
            $current = $this->request_schemev();
            $latest = $this->get_latest_schemev();
            $current1 = $current + 1;
            if((int)$current < (int)$latest){
                Exceptions::error_rep("Database scheme is not up to date. Please migrate your database immediately! | Missing upgrades: {$current1}-{$latest}");
            } elseif((int)$current > (int)$latest){
                Exceptions::error_rep("You have somehow managed to be more than up to date for your database. Your database is probably defect and needs manual repair.");
            }
        }

        private function get_upgrade_file($scheme){
            Exceptions::error_rep("Getting upgrade file for scheme {$scheme}...");
            return __DIR__ . "/data/migrations/{$scheme}.sql";
        }

        public function perform_migration($updateToSchemeV){
            Exceptions::error_rep("Performing database migration to scheme from {$this->request_schemev()} to {$updateToSchemeV}...");
            $current = $this->request_schemev();
            $path = $this->get_upgrade_file($updateToSchemeV);
            if(($current + 1) == $updateToSchemeV){
                Exceptions::error_rep("Upgrading database scheme to {$updateToSchemeV}...");
                $db = Arbeitszeit::get_app_ini()["mysql"];
                $command = "mysql --user={$db["db_user"]} --password={$db["db_password"]} -h {$db["db_host"]} -D {$db["db"]} < {$path}";
                $output = shell_exec($command);
                if($output == ""){
                    Exceptions::error_rep("Successfully migrated database to scheme {$updateToSchemeV}");
                    return true;
                } else {
                    Exceptions::error_rep("An error occured while migrating database: {$output}");
                    return false;
                }
            } else {
                Exceptions::error_rep("Cannot upgrade database scheme: You are missing other updates! | Tried scheme: {$updateToSchemeV} - Current: {$current}");
                return false;
            }
        }
    }
}


?>
