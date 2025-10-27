<?php
namespace Arbeitszeit {
    class Projects extends Arbeitszeit
    {
        public $db;

        public function __construct()
        {
            $this->db = new DB;
        }

        public function addProject($name, $items_assoc, $description = null, $deadline = null, $owner = null)
        {
            Exceptions::error_rep("[PROJECTS] Adding project...");
            if ($items_assoc == null) {
                $items_assoc = rand(0, 9999999);
            }
            $sql = "INSERT INTO `projects` (name, description, items_assoc, deadline, owner) VALUES (?, ?, ?, ?, ?);";

            if ($owner == null) {
                $owner = Benutzer::get_user($_SESSION["username"])["id"];
            }

            $res = $this->db->sendQuery($sql)->execute([$name, $description, $items_assoc, $deadline, $owner]);

            if (!$res) {
                Exceptions::error_rep("[PROJECTS] An error occured while creating new project. See previous message for more information...");
                return false;
            } else {
                return true;
            }
        }

        public function editProject($projectId, $changes)
        {
            Exceptions::error_rep("[PROJECTS] Editing project '{$projectId}'...");
            $changes = [
                "name" => $changes["name"],
                "description" => $changes["description"],
                "deadline" => $changes["deadline"],
                "owner" => $changes["owner"]
            ];

            foreach ($changes as $change => $val) {
                $sql = "UPDATE `projects` SET `$change` = ?";
                $res = $this->db->sendQuery($sql)->execute([$val]);
                Exceptions::error_rep("[PROJECTS] Changing project '{$projectId}'. Attribute: '{$change}' | Value: '{$val}'");
                if (!$res) {
                    Exceptions::error_rep("An error returned while editing project '{$projectId}'. See previous message for more information.");
                    return false;
                }
            }
            Exceptions::error_rep("[PROJECTS] Successfully edited project '{$projectId}'.");
            return true;

        }

        public function deleteProject($id)
        {
            Exceptions::error_rep("[PROJECTS] Deleting project '{$id}'...");
            $sql = "DELETE FROM projects WHERE id = ?";
            $res = $this->db->sendQuery($sql)->execute([$id]);
            if (!$res) {
                Exceptions::error_rep("[PROJECTS] An error occurred while deleting an project. See previous message for more information.");
                return false;
            } else {
                Exceptions::error_rep(message: "[PROJECTS] Successfully deleted project '{$id}'.");
                return true;
            }
        }


        public function getProjectUsers($project_id): array|bool
        {
            Exceptions::error_rep("[PROJECTS] Getting users for project '{$project_id}'...");

            $sql = "SELECT * FROM projects_users WHERE projectid = ?";
            $stmt = $this->db->sendQuery($sql);
            $res = $stmt->execute([$project_id]);

            if (!$res) {
                Exceptions::error_rep("[PROJECTS] Error while getting project users for project '{$project_id}'");
                return false;
            }

            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if (empty($rows)) {
                return false;
            }

            $arr = [];
            foreach ($rows as $row) {
                $arr[$row["id"]] = ["name" => $this->benutzer()->get_user_from_id($row["userid"])["name"], "role" => $row["role"], "userid" => $row["userid"]]; // key by id
            }

            return $arr;
        }


        public function addProjectMember($project_id, $user_id, $permissions = 0, $role = "Member")
        {
            Exceptions::error_rep("[PROJECTS] Adding user to project '{$project_id}'...");
            // check if user exists
            if (!$this->benutzer()->get_current_user()) {
                return false;
            }
            if (!$this->getProject($project_id)) {
                return false;
            }
            // PERMS: 0 = member, 1 = project admin, 2 = admin
            $sql = "INSERT INTO `projects_users` (userid, projectid, permissions, role, is_owner) VALUES (?, ?, ?, ?, ?)";
            $res = $this->db->sendQuery($sql)->execute([$user_id, $project_id, $permissions, $role, 0]);

            if (!$res) {
                return false;
            } else {
                return true;
            }
        }

        public function getCurrentUserProjects()
        {
            if (!$this->benutzer()->get_current_user()) {
                return false;
            }

            $id = $this->benutzer()->get_current_user()["id"];
            #$sql = "SELECT * FROM projects_users WHERE userid = ?";
            if ($this->benutzer()->is_admin($this->benutzer()->get_current_user())) {
                $sql = "SELECT * FROM projects";
                $res = $this->db->simpleQuery($sql);
            } else {
                $sql = "SELECT * FROM projects_users WHERE userid = ?";
                $res1 = $this->db->sendQuery($sql)->execute([$id]);
                if (!$res1) {
                    return false;
                } else {
                    if ($res1->rowCount == 0) {
                        return false;
                    }
                    $user_projects = [];
                    while ($row = $res1->fetch(\PDO::FETCH_ASSOC)) {
                        array_push($user_projects, $row["projectid"]);
                    }
                    $placeholder = implode(",", array_fill(0, count($user_projects), "?"));
                    $sql = "SELECT * FROM projects WHERE id IN ($placeholder)";
                    $res = $this->db->sendQuery($sql)->execute([$user_projects]);
                }
            }

            if (!$res) {
                return false;
            } else {
                $arr = [];
                while ($row = $res->fetch(\PDO::FETCH_ASSOC)) {
                    $arr[$row["id"]] = $row;
                }
                return $arr;

            }
        }

        public function addProjectItem($project_id, $title, $description, $assignee = null)
        {
            $sql = "INSERT INTO `projects_items` (pid, title, description, assignee) VALUES (?, ?, ?, ?)";
            $res = $this->db->sendQuery($sql)->execute([$project_id, $title, $description, $assignee]);

            if (!$res) {
                return false;
            } else {
                return true;
            }
        }

        public function mapWorktimeToItem($worktime_id, $item_id, $user_id = null)
        {
            if ($user_id = null) {
                if (!$this->benutzer()->get_current_user()) {
                    return false;
                }

                $sql = "INSERT INTO `projects_worktimes` (itemid, worktimeid, user) VALUES (?, ?, ?)";
                $res = $this->db->sendQuery($sql)->execute([$item_id, $worktime_id, $user_id]);

                if (!$res) {
                    return false;
                } else {
                    return true;
                }
            }
        }

        public function getProjects()
        {
            Exceptions::error_rep("[PROJECTS] Fetching projects...");
            $sql = "SELECT id FROM projects";
            $res = $this->db->sendQuery($sql);
            $res->execute();
            if (!$res) {
                Exceptions::error_rep("[PROJECTS] An error occurred while fetching projects - there just may be none. See previous message for more information.");
                return false;
            } else {
                Exceptions::error_rep("[PROJECTS] Successfully found projects.");
                return $res->fetch(\PDO::FETCH_ASSOC);
            }
        }

        public function getProject($id)
        {
            Exceptions::error_rep("[PROJECTS] Fetching project '{$id}'...");
            $sql = "SELECT * FROM projects WHERE id = ?";
            $res = $this->db->sendQuery($sql);
            $res->execute([$id]);
            if (!$res) {
                Exceptions::error_rep("[PROJECTS] An error occurred while fetching project '{$id}'. See previous message for more information.");
                return false;
            } else {
                Exceptions::error_rep("[PROJECTS] Successfully found project '{$id}'.");
                return $res->fetch(\PDO::FETCH_ASSOC);
            }
        }

        public function checkUserisOwner($project_id)
        {
            $project = $this->getProject($project_id);
            $user = $this->benutzer()->get_current_user();

            if ($user["id"] == $project["owner"] || $user["isAdmin"] == true) {
                return true;
            } else {
                return false;
            }
        }

        public function checkUserHasProjectAccess($user_id, $project_id, $required = 0)
        {
            if (isset($project_id)) {
                $sql = "SELECT projectid, permissions FROM projects_users WHERE userid = ? AND projectid = ?";
                $res = $this->db->sendQuery($sql)->execute([$user_id, $project_id]);
            } else {
                $sql = "SELECT projectid, permissions FROM projects_users WHERE userid = ?";
                $res = $this->db->sendQuery($sql)->execute([$user_id]);
            }

            if (!$res) {
                return false;
            }

            if (count($res->fetch(\PDO::FETCH_ASSOC)) > 1) {
                Exceptions::error_rep("[PROJECTS] An error occured while checking user project permissions. There might be a duplicate entry for the permissions of project '{$project_id}' and user '{$user_id}'");
                return false;
            }

            while ($data = $res->fetch(\PDO::FETCH_ASSOC)) {
                if ($data["projectid"] == $project_id && $data["permissions"] >= 0) {
                    return true;
                }
            }
            return false;

        }

        public function getProjectByName($name)
        {
            Exceptions::error_rep("[PROJECTS] Fetching project by name '{$name}'.");
            $sql = "SELECT * FROM projects WHERE name = ?";
            $res = $this->db->sendQuery($sql)->execute([$name]);

            if (!$res) {
                Exceptions::error_rep("[PROJECTS] An error occured while fetching project. See previous messages.");
                return false;
            } else {
                return $res->fetch(\PDO::FETCH_ASSOC);
            }
        }

        public function getUserProjects($user)
        {
            Exceptions::error_rep("[PROJECTS] Fetching projects for user ID '{$user}'...");
            $sql = "SELECT * FROM projects WHERE id = ?";
            $res = $this->db->sendQuery($sql)->execute([$user]);
            if (!$res) {
                Exceptions::error_rep("[PROJECTS] An error occured while fetching user projects for user ID '{$user}'. User might also have no projects... See previous message for more information.");
                return false;
            } else {
                $projects = [];
                Exceptions::error_rep("[PROJECTS] Successfully found projects for user ID '{$user}'.");
                while ($data = $res->fetch(\PDO::FETCH_ASSOC)) {
                    array_push($items, $data);
                }
                return $projects;
            }
        }

        public function getProjectItems($project_id): array|bool
        {
            Exceptions::error_rep("[PROJECTS] Fetching project items for project '{$project_id}'...");

            $sql = "SELECT * FROM projects_items WHERE pid = ?";
            $stmt = $this->db->sendQuery($sql);
            $res = $stmt->execute([$project_id]);

            if (!$res) {
                Exceptions::error_rep("[PROJECTS] Error fetching project items for '{$project_id}'.");
                return false;
            }

            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if (empty($rows)) {
                return false;
            }

            Exceptions::error_rep("[PROJECTS] Successfully fetched project items for '{$project_id}'.");
            return $rows;
        }


        public function getUserProjectItems($project_id, $user)
        {
            Exceptions::error_rep("[PROJECTS] Fetching project items for user '{$user}' and project '{$project_id}'...");
            $sql = "SELECT * FROM projects_items WHERE assignee = ? AND pid = ?";
            $stmt = $this->db->sendQuery($sql);

            if (!$stmt->execute([$user, $project_id])) {
                Exceptions::error_rep("[PROJECTS] Error while fetching user project items.");
                return false;
            }

            $items = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            Exceptions::error_rep("[PROJECTS] Successfully fetched " . count($items) . " items.");
            return $items;
        }


        public function getItem($id): array|bool
        {
            $sql = "SELECT * FROM projects_items WHERE pid = ?";
            $stmt = $this->db->sendQuery($sql);
            $res = $stmt->execute([$id]);

            if (!$res) {
                return false;
            }

            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            return $row ?: false;
        }

        public function getUserProjectWorktimes($item_id): array|bool
        {
            Exceptions::error_rep("[PROJECTS] Fetching worktimes for item '{$item_id}'...");

            $sql = "SELECT * FROM projects_worktimes WHERE itemid = ?";
            $stmt = $this->db->sendQuery($sql);
            $res = $stmt->execute([$item_id]);

            if (!$res) {
                Exceptions::error_rep("[PROJECTS] Error while fetching worktimes for item '{$item_id}'.");
                return false;
            }

            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if (empty($rows)) {
                return false;
            }

            Exceptions::error_rep("[PROJECTS] Successfully fetched " . count($rows) . " worktimes for item '{$item_id}'.");
            return $rows;
        }

        public function getProjectWorktimes($project_id): array|bool
        {
            Exceptions::error_rep("[PROJECTS] Fetching worktimes for project '{$project_id}'...");

            $sql = "SELECT * FROM projects_worktimes WHERE projectid = ?";
            $stmt = $this->db->sendQuery($sql);
            $res = $stmt->execute([$project_id]);

            if (!$res) {
                Exceptions::error_rep("[PROJECTS] Error while fetching worktimes for project '{$project_id}'.");
                return false;
            }

            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if (empty($rows)) {
                return false;
            }

            Exceptions::error_rep("[PROJECTS] Successfully fetched " . count($rows) . " worktimes for project '{$project_id}'.");
            return $rows;
        }



    }
}

?>