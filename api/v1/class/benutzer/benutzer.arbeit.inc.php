<?php
namespace Arbeitszeit {

    use Arbeitszeit\Events\EventDispatcherService;
    use Symfony\Component\EventDispatcher\EventDispatcher;
    use Arbeitszeit\Events\UserCreatedEvent;
    use Arbeitszeit\Events\UserDeletedEvent;
    class Benutzer extends Arbeitszeit
    {

        public $i18n;
        public $db;
        public function __construct()
        {
            $this->db = $this->db();
            $this->i18n = $this->i18n()->loadLanguage(null, "class/benutzer");
        }

        /**
         * create_user - Create a user
         * 
         * @param string $username Username of the employee
         * @param string $name First name of the employee
         * @return array|bool Returns true on success and an array otherwise
         */
        public function create_user($username, $name, $email, $password, $isAdmin = 0)
        {
            if($this->nodes()->checkNode("benutzer.inc", "create_user") == false){
                return false;
            }
            Exceptions::error_rep("Creating user '$username'...");
            $password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO `users` (`name`, `username`, `email`, `password`, `email_confirmed`, `isAdmin`) VALUES (?, ?, ?, ?, '1', ?);";
            $data = $this->db->sendQuery($sql)->execute([$name, $username, $email, $password, $isAdmin]);
            if ($data == false) {
                Exceptions::error_rep("An error occurred while creating a user. See previous message for more information");
                return [
                    "error" => [
                        "error_code" => 3,
                        "error_message" => "Error while creating a user!"
                    ]
                ];
            } else {
                Exceptions::error_rep("User '$username' created successfully.");
                EventDispatcherService::get()->dispatch(new UserCreatedEvent($username, $email, $isAdmin), UserCreatedEvent::NAME);
                return true;
            }
        }

        /**
         * delete_user() - Deletes a user from the database
         * 
         * @param int $id ID of the user
         * @return bool|array Returns true on success and an array otherwise
         * 
         * @note This function only deletes the user but not their other data.
         */
        public function delete_user($id)
        {
            if($this->nodes()->checkNode("benutzer.inc", "delete_user") == false){
                return false;
            }
            $user = $this->get_user_from_id($id);	
            $username = $user["username"];
            $email = $user["email"];
            Exceptions::error_rep("Deleting user with id '$id'...");
            $sql = "DELETE FROM `users` WHERE id = ?;";
            $data = $this->db->sendQuery($sql)->execute([$id]);
            if ($data == false) {
                Exceptions::error_rep("An error occurred while deleting an user. See previous message for more information");
                return [
                    "error" => [
                        "error_code" => 4,
                        "error_message" => "Error while deleting user!"
                    ]
                ];
            } else {
                Exceptions::error_rep("User with id '$id' deleted successfully.");
                EventDispatcherService::get()->dispatch(new UserDeletedEvent($username, $email), UserDeletedEvent::NAME);
                return true;
            }
        }

        /**
         * delete_user() - Deletes a user from the database	
         * @param string $username
         * @return array|bool Returns false on failure and an array otherwise
         */
        public static function get_user($username)
        {
            Exceptions::error_rep("Getting user '$username'...");
            $sql = "SELECT * FROM `users` WHERE username = ?;";
            $db = new DB;
            $res = $db->sendQuery($sql);
            $res->execute([$username]);
            $count = $res->rowCount();
            if ($count == 1) {
                $data = $res->fetch(\PDO::FETCH_ASSOC);
                Exceptions::error_rep("User '$username' found.");
                return $data;
            } else {
                Exceptions::error_rep("Could not find user '$username'.");
                return false;
            }
        }

        /**
         * get_user_from_id() - Gets a user from the database
         * @param int $id
         * @return array|bool Returns false on failure and an array otherwise
         */
        public static function get_user_from_id($id)
        {
            Exceptions::error_rep("Getting user with id '$id'...");
            $conn = new DB();
            $sql = "SELECT * FROM `users` WHERE id = ?;";
            $res = $conn->sendQuery($sql);
            $res->execute([$id]);
            if ($res->rowCount() == 1) {
                $data = $res->fetch(\PDO::FETCH_ASSOC);
                Exceptions::error_rep("User with id '$id' found.");
                return $data;
            } else {
                Exceptions::error_rep("Could not find user with id '$id'.");
                return false;
            }
        }

        /**
         * get_user_from_email() - Gets a user from the database
         * @param string $email
         * @return array|bool Returns false on failure and an array otherwise
         */
        public static function get_user_from_email($email)
        {
            Exceptions::error_rep("Getting user with email '$email'...");
            $conn = new DB();
            $sql = "SELECT * FROM `users` WHERE email = ?;";
            $res = $conn->sendQuery($sql);
            $res->execute([$email]);
            if ($res->rowCount() == 1) {
                $data = $res->fetch(\PDO::FETCH_ASSOC);
                Exceptions::error_rep("User with email '$email' found.");
                return $data;
            } else {
                Exceptions::error_rep("Could not find user with email '$email'.");
                return false;
            }
        }

        /**
         * get_username_from_email() - Gets a username from the database
         * @param string $email
         * @return string|bool Returns false on failure and a string otherwise
         */
        public function get_username_from_email($email)
        {
            Exceptions::error_rep("Getting username with email '$email'...");
            $conn = new DB();
            $sql = "SELECT username FROM `users` WHERE email = ?;";
            $res = $conn->sendQuery($sql);
            $res->execute([$email]);
            if ($res->rowCount() == 1) {
                $data = $res->fetch(\PDO::FETCH_ASSOC);
                Exceptions::error_rep("Username with email '$email' found.");
                return $data["username"];
            } else {
                Exceptions::error_rep("Could not find user with email '$email'.");
                return false;
            }
        }

        /**
         * get_all_users() - Gets all users from the database
         * @return array|bool Returns false on failure and an array otherwise
         */
        public function get_all_users()
        {
            Exceptions::error_rep("Getting all users...");
            $sql = "SELECT * FROM `users`;";
            $res = $this->db->sendQuery($sql);
            $res->execute();
            $count = $res->rowCount();
            $dat = [];
            if ($count >= 1) {
                while ($data = $res->fetch(\PDO::FETCH_ASSOC)) {
                    $dat[$data["id"]] = $data;
                }
                Exceptions::error_rep("Users found and returing data.");
                return $dat;
            } else {
                Exceptions::error_rep("Could not get users. Please check the database connection.");
                return false;
            }
        }

        /**
         * get_all_users_html() - Gets all users from the database
         * @return string Returns a string (rendered HTML) on success
         */
        public function get_all_users_html()
        {
            if($this->nodes()->checkNode("benutzer.inc", "get_all_users_html") == false){
                return false;
            }
            Exceptions::error_rep("Getting all users...");
            $base_url = $ini = $this->get_app_ini()["general"]["base_url"];
            $sql = "SELECT * FROM `users`;";
            $res = $this->db->sendQuery($sql);
            $res->execute();
            $count = $res->rowCount();

            if ($count == 0) {
                Exceptions::failure("ERR-NO-USERS", "No users found?", "N/A");
                return "<p>{$this->i18n["no_users"]}</p>";
            }
            while ($data = $res->fetch(\PDO::FETCH_ASSOC)) {
                if ($data["username"] == "api") {
                    $data["email"] = "System user";
                }

                $name = $data["name"];
                $email = $data["email"];
                $username = $data["username"];
                $id = $data["id"];

                $html = <<<DOC
                <tr>
                    <td><a href='http://{$base_url}/suite/admin/actions/users/delete_user.php?id={$id}'>{$this->i18n["delete_user"]}</a> | <a href='http://{$base_url}/suite/plugins/index.php?pn=userdetail&p_view=views/user.php&user={$username}'>{$this->i18n["edit_user"]}</a></td>
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

        /**
         * get_user_html() - Gets a user from the database
         * @param string $username
         * @return string Returns a string (rendered HTML) on success
         */
        public function get_user_html($username)
        {
            if($this->nodes()->checkNode("benutzer.inc", "get_user_html") == false){
                return false;
            }
            Exceptions::error_rep("Getting user '$username'...");
            $base_url = $ini = $this->get_app_ini()["general"]["base_url"];
            $data = $this->get_user($username);
            if ($data == false) {
                Exceptions::error_rep("An error occurred while generating user html. User '$username' is either not logged in or the session expired.");
                return "<p>{$this->i18n["unknown_error"]}</p>";
            }
            while ($data) {
                $reww = $data["name"];
                $rol = $data["id"];
                $ral = $data["email"];
                $rww = $data["username"];

                $html = <<<DATA

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

        /**
         * is_admin() - Checks if a user is an admin
         * 
         * Should be accessed like this: $arbeit->benutzer()->is_admin($arbeit->benutzer()->get_user($_SESSION["username"]));
         * @param array $user
         * @return bool Returns true if the user is an admin and false otherwise
         */
        public static function is_admin($user)
        {
            if ($user["isAdmin"] == true) {
                return true;
            } else {
                return false;
            }
        }

        public function editUserProperties(mixed $username_or_id, string $name, mixed $value): bool
        {
            if($this->nodes()->checkNode("benutzer.inc", "editUserProperties") == false){
                return false;
            }
            if (
                !isset($_SESSION["username"]) ||
                !$this->is_admin($this->get_user($_SESSION["username"]))
            ) {
                Exceptions::error_rep("Unauthorized attempt by user '{$_SESSION["username"]}' to update user properties!");
                return false;
            }

            $allowed_types = ["username", "email", "isAdmin", "name"];
            if (!in_array($name, $allowed_types)) {
                Exceptions::error_rep("Could not update user entry – invalid property '{$name}'");
                return false;
            }

            if (is_int($username_or_id)) {
                $search_type = "id";
            } elseif (is_string($username_or_id)) {
                $search_type = "username";
            } else {
                Exceptions::error_rep("Invalid identifier type for user update.");
                return false;
            }

            Exceptions::error_rep("Updating user entry '{$username_or_id}' (Type: {$search_type}); Property: {$name}; Value: {$value}");

            $sql = "UPDATE `users` SET `$name` = ? WHERE `$search_type` = ?";
            try {
                $stmt = $this->db->sendQuery($sql);
                $success = $stmt->execute([$value, $username_or_id]);
            } catch (\Exception $e) {
                Exceptions::error_rep("Database exception while updating user: " . $e->getMessage());
                return false;
            }

            if ($success) {
                Exceptions::error_rep("Successfully updated user entry '{$username_or_id}' (Type: {$search_type})");
                return true;
            } else {
                Exceptions::error_rep("Update failed for user '{$username_or_id}' (Type: {$search_type})");
                return false;
            }
        }
    }
}



?>