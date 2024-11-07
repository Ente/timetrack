<?php
namespace Arbeitszeit {

    use PDOException;

    class DB extends Arbeitszeit {
        private array $config;

        private \PDO $connection;


        public function __construct(){
            $this->__set("config", Arbeitszeit::get_app_ini()["mysql"]);
            $c = $this->__get("config");
            try {
            $this->__set("connection", new \PDO($this->dsnStringBuilder($c["db"], $c["db_host"]), $c["db_user"], $c["db_password"]));
            } catch (PDOException $e){
                Exceptions::error_rep("Could not establish a connection to the database. | Error message: " . $e->getMessage());
                Exceptions::failure($e->getCode(), $e->getMessage(), $e->getTraceAsString());
                die();
            }
        }

        public function __set($name, $value){
            $this->$name = $value;
        }

        public function __get($name){
            return $this->$name;
        }

        private function dsnStringBuilder($db, $host){
            return "mysql:dbname=" . $db . ";host=" . $host;
        }

        public function sendQuery($sql){
            try {
                $pdo = $this->__get("connection");
                $st = $pdo->prepare($sql);
                return $st;
            } catch (PDOException $e){
                Exceptions::error_rep("[DB] An error occured while performing a query. | Error message: " . $e->getMessage());
                return false;
            }
        }

        public function simpleQuery($sql){
            try {
                $pdo = $this->__get("connection");
                return $pdo->query($sql);
            } catch (PDOException $e){
                Exceptions::error_rep("[DB] An error occured while performing a simple query. | Error message: " . $e->getMessage());
                return false;
            }
        }
    }
}


?>