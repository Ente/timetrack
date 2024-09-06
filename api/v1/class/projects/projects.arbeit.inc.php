<?php
namespace Arbeitszeit {
    class Projects extends Arbeitszeit {
        public $db;

        public function __construct(){
            $this->db = new DB;
        }

        public function addProjectE($name, $description, $note, $users = null){
            $sql = "INSERT INTO projects (id, name, users, description, note) VALUES (0, ?, ?, ?, ?);";
            $res = $this->db->sendQuery($sql)->execute([$name, $description, $note, $this->computeProjectUserArray("json", $users)]);
            if(!$res){
                Exceptions::error_rep("[PROJECTS] An error occured while creating an project. See previous message for more information.");
                return false;
            } else {
                return true;
            }
        }

        public function deleteProject($id){
            $sql = "DELETE FROM projects WHERE id = ?";
            $res = $this->db->sendQuery($sql)->execute([$id]);
            if(!$res){
                Exceptions::error_rep("[PROJECTS] An error occured while deleting an project. See previous message for more information.");
                return false;
            } else {
                return true;
            }
        }

        public function computeProjectUserArray($type = "array", $usernames = null){
            $user = [];
            $i = 0;
            foreach($usernames as $user){
                $user[$i] = $user;
                ++$i;
            }
            if($type != "array"){
                return json_encode($user);
            } else {
                return $user;
            }
        }

        public function getProjectUsers($id){
            return json_decode($this->getProject($id)["users"]);
        }

        public function getProjects(){
            $sql = "SELECT id FROM projects";
            $res = $this->db->sendQuery($sql);
            $res->execute();
            if(!$res){
                Exceptions::error_rep("[PROJECTS] An error occured while fetching projects - there just may be none. See previous message for more information.");
                return false;
            } else {
                return $res->fetch(\PDO::FETCH_ASSOC);
            }
        }

        public function getProject($id){
            $sql = "SELECT * FROM projects WHERE id = ?";
            $res = $this->db->sendQuery($sql);
            $res->execute([$id]);
            if(!$res){
                Exceptions::error_rep("[PROJECTS] An error occured while fetching project '{$id}'. See previous message for more information.");
                return false;
            } else {
                return $res->fetch(\PDO::FETCH_ASSOC);
            }
        }

        public function getUserProjects($username){
            $projects = $this->getProjects();
            $userProjects = [];
            $i = 0;
            foreach($projects as $pr){
                $pr1 = $this->getProject($pr);
                $users = json_decode($pr1["users"]);
                foreach($users as $user){
                    if($user == $username){
                        $userProjects[$i] = $pr1["id"];
                    }
                }
                ++$i;
            }
            return $userProjects;
        }
    }
}

?>