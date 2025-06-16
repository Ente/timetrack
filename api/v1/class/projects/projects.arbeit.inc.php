<?php
namespace Arbeitszeit {
    class Projects extends Arbeitszeit {
        public $db;

        public function __construct(){
            $this->db = new DB;
        }

        public function addProjectE($name, $description, $note, $users = null){
            Exceptions::error_rep("[PROJECTS] Adding project '{$name}'...");
            $sql = "INSERT INTO projects (id, name, users, description, note) VALUES (0, ?, ?, ?, ?);";
            $res = $this->db->sendQuery($sql)->execute([$name, $description, $note, $this->computeProjectUserArray("json", $users)]);
            if(!$res){
                Exceptions::error_rep("[PROJECTS] An error occurred while creating an project. See previous message for more information.");
                return false;
            } else {
                Exceptions::error_rep("[PROJECTS] Successfully created project '{$name}'.");
                return true;
            }
        }

        public function deleteProject($id){
            Exceptions::error_rep("[PROJECTS] Deleting project '{$id}'...");
            $sql = "DELETE FROM projects WHERE id = ?";
            $res = $this->db->sendQuery($sql)->execute([$id]);
            if(!$res){
                Exceptions::error_rep("[PROJECTS] An error occurred while deleting an project. See previous message for more information.");
                return false;
            } else {
                Exceptions::error_rep("[PROJECTS] Successfully deleted project '{$id}'.");
                return true;
            }
        }

        public function computeProjectUserArray($type = "array", $usernames = null){
            Exceptions::error_rep("[PROJECTS] Computing user array...");
            $user = [];
            $i = 0;
            foreach($usernames as $user){
                $user[$i] = $user;
                ++$i;
            }
            if($type != "array"){
                return json_encode($user);
            } else {
                Exceptions::error_rep("[PROJECTS] Could not compute user array. Returning default.");
                return $user;
            }
        }

        public function getProjectUsers($id){
            Exceptions::error_rep("[PROJECTS] Getting users for project '{$id}'...");
            return json_decode($this->getProject($id)["users"]);
        }

        public function getProjects(){
            Exceptions::error_rep("[PROJECTS] Fetching projects...");
            $sql = "SELECT id FROM projects";
            $res = $this->db->sendQuery($sql);
            $res->execute();
            if(!$res){
                Exceptions::error_rep("[PROJECTS] An error occurred while fetching projects - there just may be none. See previous message for more information.");
                return false;
            } else {
                Exceptions::error_rep("[PROJECTS] Successfully found projects.");
                return $res->fetch(\PDO::FETCH_ASSOC);
            }
        }

        public function getProject($id){
            Exceptions::error_rep("[PROJECTS] Fetching project '{$id}'...");
            $sql = "SELECT * FROM projects WHERE id = ?";
            $res = $this->db->sendQuery($sql);
            $res->execute([$id]);
            if(!$res){
                Exceptions::error_rep("[PROJECTS] An error occurred while fetching project '{$id}'. See previous message for more information.");
                return false;
            } else {
                Exceptions::error_rep("[PROJECTS] Successfully found project '{$id}'.");
                return $res->fetch(\PDO::FETCH_ASSOC);
            }
        }

        public function getUserProjects($username){
            Exceptions::error_rep("[PROJECTS] Fetching projects for user '{$username}'...");
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
            Exceptions::error_rep("[PROJECTS] Returning found projects for user '{$username}'.");
            return $userProjects;
        }
    }
}

?>