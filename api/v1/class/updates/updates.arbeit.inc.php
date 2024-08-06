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

        public function request_schemev(){
            $conn = Arbeitszeit::get_conn();

            try {
                $sql = "SELECT * FROM scheme;";
                $res = mysqli_query($conn, $sql);
                $data = mysqli_fetch_assoc($res)["v"];
            } catch(\Exception $e){
                $sql = "CREATE TABLE scheme (v INT DEFAULT 1);";
                $sql1 = "INSERT INTO scheme (v) VALUES (1);";
                mysqli_query($conn, $sql);
                mysqli_query($conn, $sql1);
                $this->request_schemev();
            }

            return $data;
        }

        public function get_latest_schemev(){
            $dir = dirname(__DIR__) . "/updates/data/scheme.db";
            $d = file_get_contents($dir);
            return $d;
        }

        public function compare_scheme(){
            $current = $this->request_schemev();
            $latest = $this->get_latest_schemev();
            $current1 = $current + 1;
            if((int)$current < (int)$latest){
                Exceptions::error_rep("Database scheme is not up to date. Please migrate your database immediately! | Missing upgrades: {$current1}-{$latest}");
            } elseif((int)$current > (int)$latest){
                Exceptions::error_rep("You have somehow managed to be more than up to date for your database. Your database is probably defect and needs manual repair.");
            }
        }

        public function get_upgrade_file($scheme){
            return __DIR__ . "/data/migrations/{$scheme}.sql";
        }

        public function perform_migration($updateToSchemeV){
            $current = $this->request_schemev();
            $path = $this->get_upgrade_file($updateToSchemeV);
            if(($current + 1) == $updateToSchemeV){
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
