<?php
namespace Arbeitszeit{
    class Benutzer extends Arbeitszeit{

        /**
         * create_user - Erstellt einen Nutzer in der Datenbank
         * 
         * @param string $username Der Nutzername des Mitarbeiters
         * @param string $name Vorname des Nutzers
         * @return array|bool Gibt "true" bei Erfolg zurück und ein Fehler-Array bei einem Fehler
         */
        public function create_user($username, $name, $email, $password, $isAdmin = 0){
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
                return "<p>Keine Nutzer vorhanden!</p>";
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
                    <td><a href='http://{$base_url}/suite/admin/actions/users/delete_user.php?id={$id}'>Benutzer löschen</a></td>
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
                return "<p>Ein Fehler ist aufgetreten: Entweder ist die Sitzung abgelaufen oder du hast es geschafft, die Seite anders aufzurufen!\nGlückwunsch!</p>";
            }
            while($data){
                $reww = $data["name"];
                $rol = $data["id"];
                $ral = $data["email"];
                $rww = $data["username"];

                $html = <<< DATA

                <div class="box">
                    <p>Dein Name: {$reww}</p>
                    <p>Deine ID: {$rol}</p>
                    <p>Deine Email: {$ral}</p>
                    <p>Dein Nutzername: {$rww}</p>
                </div>

                <br>
                <p>Du möchstest, dass deine Daten geändert werden? Bitte kontaktiere hierfür deinen Vorgesetzten.</p>

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