<?php
namespace Arbeitszeit{
    use Arbeitszeit\Hooks;
    class Benutzer extends Arbeitszeit{

        public array $i18n;
        public function __construct(){
            $i18n = new i18n;
            $this->i18n = $i18n->loadLanguage(null, "class/benutzer");
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
                $conn = parent::get_conn();
                $password = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO `users` (`name`, `username`, `email`, `password`, `email_confirmed`, `isAdmin`) VALUES ('{$name}', '{$username}', '{$email}', '{$password}', '1', '{$isAdmin}');";
                mysqli_query($conn, $sql);
                if(mysqli_error($conn)){
                    Exceptions::error_rep("An error occured while creating a user. | SQL-Error: " . mysqli_error($conn));
                    return [
                        "error" => [
                            "error_code" => 3,
                            "error_message" => "Error while creating a user!"
                        ]
                    ];
                } else {
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
            $conn = parent::get_conn();
            $sql = "DELETE FROM `users` WHERE id = '{$id}';";
            mysqli_query($conn, $sql);
            if(mysqli_error($conn)){
                Exceptions::error_rep("An error occured while deleting an user. | SQL-Error: " . mysqli_error($conn));
                return [
                    "error" => [
                        "error_code" => 4,
                        "error_message" => "Error while deleting user!"
                    ]
                ];
            } else {
                return true;
            }
        }

        public static  function get_user($username){
            $conn = Arbeitszeit::get_conn();
            $sql = "SELECT * FROM `users` WHERE username = '{$username}';";
            $res = mysqli_query($conn, $sql);
            if(mysqli_num_rows($res) == 1){
                $data = mysqli_fetch_assoc($res);
                return $data;
            } else {
                Exceptions::error_rep("Could not find user '$username'.");
                return false;
            }
        }

        public static  function get_user_from_id($id){
            $conn = Arbeitszeit::get_conn();
            $sql = "SELECT * FROM `users` WHERE id = '{$id}';";
            $res = mysqli_query($conn, $sql);
            if(mysqli_num_rows($res) == 1){
                $data = mysqli_fetch_assoc($res);
                return $data;
            } else {
                Exceptions::error_rep("Could not find user with id '$id'.");
                return false;
            }
        }

        public function get_all_users(){
            $conn = Arbeitszeit::get_conn();
            $sql = "SELECT * FROM `users`;";
            $res = mysqli_query($conn, $sql);
            $count = mysqli_num_rows($res);
            $dat = [];
            if($count >= 1){
                while($data = mysqli_fetch_assoc($res)){
                    $dat[$data["id"]] = $data;
                }
                return $dat;
            } else {
                Exceptions::error_rep("Could not get users. Please check the database connection.");
                return false;
            }
        }

        public function get_all_users_html(){
            $base_url = $ini = $this->get_app_ini()["general"]["base_url"];
            $sql = "SELECT * FROM `users`;";
            $res = mysqli_query(Arbeitszeit::get_conn(), $sql);
            $count = mysqli_num_rows($res);

            if($count == 0){
                return "<p>{$this->i18n["no_users"]}</p>";
            }
            while($data = mysqli_fetch_assoc($res)){
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

            return $html;
        }

        public function get_user_html($username){
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