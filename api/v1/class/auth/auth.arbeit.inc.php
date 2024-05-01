<?php
namespace Arbeitszeit{
    require_once $_SERVER["DOCUMENT_ROOT"] . "/vendor/autoload.php";
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;
    class Auth extends Arbeitszeit{
        
        public function login($username, $password, $option){ # "option"-> array [ "remember" => true/false, ... ]
            session_start();
            $conn = Arbeitszeit::get_conn();
            $ini = Arbeitszeit::get_app_ini();
            $base_url = $ini["general"]["base_url"];
            if(!isset($username, $password)){
                Exceptions::error_rep("Login failed for username '$username' - no data supplied. Redirecting...");
                die(header("Location: http://{$ini["general"]["base_url"]}/suite/login.php?error=nodata"));
            } else {
                $sql = "SELECT * FROM users WHERE username = '{$username}';";
                $res = mysqli_query($conn, $sql);

                if(mysqli_error($conn)){
                   Exceptions::error_rep("Login failed for username '$username' - Database connection error. Redirecting...");
                   die(header("Location: http://{$ini["general"]["base_url"]}/suite/login.php?error=nodata"));
                }

                $count = mysqli_num_rows($res);
                if($count != 1){
                    Exceptions::error_rep("Login failed for username '$username' - User not found. Redirecting...");
                    die(header("Location: http://{$ini["general"]["base_url"]}/suite/login.php?error=nodata"));
                }

                $data = mysqli_fetch_assoc($res);
                if(password_verify($password, $data["password"])){
                    $ts = time();
                    $_SESSION["logged_in"] = true;
                    $_SESSION["username"] = $username;
                    $_SESSION["time"] = date("d.m.Y H:i:s", $ts);
                    $this->store_state($username);

                    if(isset($option["remember"])){
                        setcookie("erinnern", "true", $ts+(60*60*24*30), "/");
                        setcookie("username", $username, $ts + (60*60*24*30), "/");
                    } else {
                        header("Refresh: 1; url=http://{$ini["general"]["base_url"]}/suite");
                    }
                } else {
                    Exceptions::error_rep("Login failed for username '$username' - Incorrect credentials. Redirecting...");
                    die(header("Location: http://{$base_url}/suite/login.php?error=wrongdata"));
                }
            }
        }

        public function login_validation(){
            $ini = Arbeitszeit::get_app_ini();
            $baseurl = $ini["general"]["base_url"];
            @session_start();
            if(isset($_SESSION["logged_in"]) == false){
                header("Location: http://{$baseurl}/suite/login.php?error=notloggedin");
            }
            if($this->get_state($_SESSION["username"]) != $_COOKIE["state"]){
                $this->remove_state($_SESSION["username"]);
                Exceptions::error_rep("State mismatch on user {$_SESSION["username"]}. Removing state and redirecting...");
                header("Location: http://{$baseurl}/suite/login.php?error=statemismatch");
            }# temp removed this as it causes errors
        }

        /**
         * logout() - Loggt den Nutzer aus seiner aktuellen Sitzung aus
         * 
         * @return bool Gibt true bei Erfolg zurück
         */
        public function logout(){
            session_start();
            session_unset();
            session_destroy();

            return true;
        }

        /**
         * reset_password() - Resets the user's password
         * 
         * @param int $id The ID of the user resetting his password
         * @return bool Returns true on success, false otherwise
         */
        public function reset_password($username){
            if(MailPasswordReset::mail_password_reset($username, $this->mail_init($username, true)) == 1){
                return true;
            } else {
                return false;
            }
        }

        /**
         * store_state() - Stores the state of a user in the database
         * 
         * @param string $user Username
         * @return string Returns the state
         */
        public function store_state($user){
            $state = bin2hex(random_bytes(12));
            setcookie("state", $state, null, "/");
            $_SESSION["state"] = $state;

            $sql = "UPDATE `users` SET `state` = '{$state}' WHERE `username` = '{$user}';";
            $res = mysqli_query(parent::get_conn(), $sql);
            if($res == false){
                return mysqli_error(parent::get_conn());
            } else {
                return $state;
            }
        }

        public function get_state($user){
            $sql = "SELECT state FROM users WHERE username = '{$user}';";
            $res = mysqli_query(parent::get_conn(), $sql);
            if(mysqli_num_rows($res) == 0 || mysqli_num_rows($res) > 1){
                return false;
            } else {
                return mysqli_fetch_assoc($res)["state"];
            }
        }

        public function remove_state($user){
            $sql = "UPDATE `users` SET `state` = '' WHERE `username` = '{$user}';";
            $res = mysqli_query(parent::get_conn(), $sql);
            if($res == false){
                return mysqli_error(parent::get_conn());
            } else {
                return true;
            }
        }

        public function mail_init($user, $html = false){
            $ini = Arbeitszeit::get_app_ini();
            $userdata = Benutzer::get_user($user);
            $mail = new PHPMailer(true);
            try {
                #$mail->SMTPDebug = SMTP::DEBUG_SERVER;
                $mail->isSMTP();
                $mail->Host = $ini["smtp"]["host"];
                $mail->SMTPAuth = true;
                $mail->Username = "***REMOVED***";
                $mail->Password = "***REMOVED***";
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                $mail->setFrom("***REMOVED***", "ASZE");
                $mail->addAddress($userdata["email"], $userdata["name"]);

                if($html == true){
                    $mail->isHTML(true);
                }

            } catch (Exception $e){
            }

            return $mail;
        }
    }
}




?>