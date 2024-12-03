<?php
namespace Arbeitszeit{
    use Arbeitszeit\Hooks;
    class Benutzer extends Arbeitszeit{

        public $i18n;
        public $db;
        public function __construct(){
            $this->db = $this->db();
            $this->i18n = $this->i18n()->loadLanguage(null, "class/benutzer");
        }

        /**
         * create_user - Creates a zser
         * 
         * @param string $username Username of the employee
         * @param string $name First name of the employee
         * @return array|bool Returns true on success and an array otherwise
         */
        public function create_user($username, $name, $email, $password, $isAdmin = 0){
            #$originalFunction = function($username, $name, $email, $password, $isAdmin){
                Exceptions::error_rep("Creating user '$username'...");
                $password = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO `users` (`name`, `username`, `email`, `password`, `email_confirmed`, `isAdmin`) VALUES (?, ?, ?, ?, '1', ?);";
                $data = $this->db->sendQuery($sql)->execute([$name, $username, $email, $password, $isAdmin]);
                if($data == false){
                    Exceptions::error_rep("An error occured while creating a user. See previous message for more information");
                    return [
                        "error" => [
                            "error_code" => 3,
                            "error_message" => "Error while creating a user!"
                        ]
                    ];
                } else {
                    Exceptions::error_rep("User '$username' created successfully.");
                    return true;
                }
            #};
            #return Hooks::executeWithHooks('create_user', $originalFunction, $username, $name, $email, $password, $isAdmin);
        }

        /**
         * delete_user() - Löscht einen Nutzer aus der Datenbank
         * 
         * @param int $id ID des zu löschenden Nutzers
         * @return bool|array Gibt "true" bei Erfolg und ein Fehler-Array bei einem Fehler zurück
         * 
         * @Hinweis Funktion löscht nur den Nutzer, jedoch nicht seine Daten (Arbeitszeiten)
         */
        public function delete_user($id){
            Exceptions::error_rep("Deleting user with id '$id'...");
            $sql = "DELETE FROM `users` WHERE id = ?;";
            $data = $this->db->sendQuery($sql)->execute([$id]);
            if($data == false){
                Exceptions::error_rep("An error occured while deleting an user. See previous message for more information");
                return [
                    "error" => [
                        "error_code" => 4,
                        "error_message" => "Error while deleting user!"
                    ]
                ];
            } else {
                Exceptions::error_rep("User with id '$id' deleted successfully.");
                return true;
            }
        }

        public static  function get_user($username){
            Exceptions::error_rep("Getting user '$username'...");
            $sql = "SELECT * FROM `users` WHERE username = ?;";
            $db = new DB;
            $res = $db->sendQuery($sql);
            $res->execute([$username]);
            $count = $res->rowCount();
            if($count == 1){
                $data = $res->fetch(\PDO::FETCH_ASSOC);
                Exceptions::error_rep("User '$username' found.");
                return $data;
            } else {
                Exceptions::error_rep("Could not find user '$username'.");
                return false;
            }
        }

        public static  function get_user_from_id($id){
            Exceptions::error_rep("Getting user with id '$id'...");
            $conn = new DB();
            $sql = "SELECT * FROM `users` WHERE id = ?;";
            $res = $conn->sendQuery($sql);
            $res->execute([$id]);
            if($res->rowCount() == 1){
                $data = $res->fetch(\PDO::FETCH_ASSOC);
                Exceptions::error_rep("User with id '$id' found.");
                return $data;
            } else {
                Exceptions::error_rep("Could not find user with id '$id'.");
                return false;
            }
        }

        public static  function get_user_from_email($email){
            Exceptions::error_rep("Getting user with email '$email'...");
            $conn = new DB();
            $sql = "SELECT * FROM `users` WHERE email = ?;";
            $res = $conn->sendQuery($sql);
            $res->execute([$email]);
            if($res->rowCount() == 1){
                $data = $res->fetch(\PDO::FETCH_ASSOC);
                Exceptions::error_rep("User with email '$email' found.");
                return $data;
            } else {
                Exceptions::error_rep("Could not find user with email '$email'.");
                return false;
            }
        }

        public function get_username_from_email($email){
            Exceptions::error_rep("Getting username with email '$email'...");
            $conn = new DB();
            $sql = "SELECT username FROM `users` WHERE email = ?;";
            $res = $conn->sendQuery($sql);
            $res->execute([$email]);
            if($res->rowCount() == 1){
                $data = $res->fetch(\PDO::FETCH_ASSOC);
                Exceptions::error_rep("Username with email '$email' found.");
                return $data["username"];
            } else {
                Exceptions::error_rep("Could not find user with email '$email'.");
                return false;
            }
        }

        public function get_all_users(){
            Exceptions::error_rep("Getting all users...");
            $sql = "SELECT * FROM `users`;";
            $res = $this->db->sendQuery($sql);
            $res->execute();
            $count = $res->rowCount();
            $dat = [];
            if($count >= 1){
                while($data = $res->fetch(\PDO::FETCH_ASSOC)){
                    $dat[$data["id"]] = $data;
                }
                Exceptions::error_rep("Users found and returing data.");
                return $dat;
            } else {
                Exceptions::error_rep("Could not get users. Please check the database connection.");
                return false;
            }
        }

        public function get_all_users_html(){
            Exceptions::error_rep("Getting all users...");
            $base_url = $ini = $this->get_app_ini()["general"]["base_url"];
            $sql = "SELECT * FROM `users`;";
            $res = $this->db->sendQuery($sql);
            $res->execute();
            $count = $res->rowCount();

            if($count == 0){
                Exceptions::failure("ERR-NO-USERS", "No users found?", "N/A");
                return "<p>{$this->i18n["no_users"]}</p>";
            }
            while($data = $res->fetch(\PDO::FETCH_ASSOC)){
                if($data["username"] == "api"){
                    $data["email"] = "System user";
                }

                $name = $data["name"];
                $email = $data["email"];
                $username = $data["username"];
                $id = $data["id"];

                $html = <<< DOC
                <tr>
                    <td><a href='http://{$base_url}/suite/admin/actions/users/delete_user.php?id={$id}'>{$this->i18n["delete_user"]}</a></td>
                    <td>$name</td>
                    <td>$username</td>
                    <td>$email</td>
                </tr>

                DOC;

                echo $html;
            }
            Exceptions::error_rep("Users found and returing data.");
            return $html;
        }

        public function get_user_html($username){
            Exceptions::error_rep("Getting user '$username'...");
            $base_url = $ini = $this->get_app_ini()["general"]["base_url"];
            $data = $this->get_user($username);
            if($data == false){
                Exceptions::error_rep("An error occured while generating user html. User '$username' is either not logged in or the session expired.");
                return "<p>{$this->i18n["unknown_error"]}</p>";
            }
            while($data){
                $reww = $data["name"];
                $rol = $data["id"];
                $ral = $data["email"];
                $rww = $data["username"];

                $html = <<< DATA

                <div class="box">
                    <p>{$this->i18n["name"]}: {$reww}</p>
                    <p>{$this->i18n["id"]}: {$rol}</p>
                    <p>{$this->i18n["email"]}: {$ral}</p>
                    <p>{$this->i18n["username"]}: {$rww}</p>
                </div>

                <br>
                <p>{$this->i18n["change_request"]}</p>

                DATA;
            }
            Exceptions::error_rep("User '$username' found and returning data.");
            return $html;
        }

        public function is_admin($user){
            if($user["isAdmin"] == true){
                return true;
            } else {
                return false;
            }
        }
    }
}



?>