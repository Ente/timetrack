<?php
namespace Arbeitszeit{

    /**
     * Beinhaltet wesentliche Inhalte, wie Einstellungen, Arbeitszeiten erstellen, etc.
     * 
     * @author Bryan Böhnke-Avan <bryan@duckerz.de>
     */
    class Arbeitszeit{

        #public function __construct($db, $db_username, $db_password, $db_host){
        #    $conn = mysqli_connect($db_host, $db_username, $db_password, $db);
        #    if(mysqli_error($conn)){
        #        return [
        #            "error" => [
        #                "error_code" => 0,
        #                "error_message" => "Error while creating a connection to the database!"
        #            ]
        #        ];
        #    } else {
        #        return $conn;
        #    }
        #}

        public function __construct(){
            if(self::get_app_ini()["general"]["debug"] == true || self::get_app_ini()["general"]["debug"] == "false"){
                #_errors", 1);
            }
        }


        /**
         * db_connect() - Verbindet zur Datenbank
         * 
         * @return \mysqli|bool Returns the mysqli object on success, false on otherwise
         */
        /*public static function db_connect(){
            $ini = parse_ini_file("inc/app.ini", true);
            $db = mysqli_connect($ini["mysql"]["db_host"], $ini["mysql"]["db_user"], $ini["mysql"]["db_password"], $ini["mysql"]["db"]);
            if($db !== false){
                return $db;
            } else {
                die();
            }

        }*/


        /**
         * delete_worktime - Löscht einen Eintrag eines Mitarbeiters
         * 
         * @param int $id ID des zu löschenden Eintrags
         * @return bool|array Wirft "true" bei erfolg und ein Array bei einem Fehler zurück
         * 
         * @Hinweis **In der Originalen Datei wurde zu `alle_arbeitszeiten.php?info=deleted_worktime` verwiesen, evt. mal ändern**
         * 
         */
        public function delete_worktime($id){
            $conn = $this->get_conn();
            $id = mysqli_real_escape_string($conn, $id);
            $sql = "DELETE FROM `arbeitszeiten` WHERE id = '{$id}';";
            mysqli_query($conn, $sql);
            if(mysqli_error($conn)){
                Exceptions::error_rep("An error occured while deleting an worktime entry. | SQL-Error: " . mysqli_error($conn));
                return [
                    "error" => [
                        "error_code" => 1,
                        "error_message" => "Error while deleting a entry from the database!"
                    ]
                ];
            } else {
                return 1;
            }

        }

        public static function add_easymode_worktime($username){
            $date = date("Y-m-d");
            $time = date("H:i");
            $conn = self::get_conn();
            $user = new Benutzer();
            $usr = $user->get_user($username);

            if(!$user->get_user($username)){
                return false;
            } else {
                $sql = "INSERT INTO `arbeitszeiten` (`name`, `id`, `email`, `username`, `schicht_tag`, `schicht_anfang`, `schicht_ende`, `ort`, `active`, `review`) VALUES ( '{$usr["name"]}', '0', '{$usr["email"]}', '{$username}', '{$date}', '{$time}', '00:00', '-', '1', '0');";
                mysqli_query($conn, $sql);
                if(mysqli_error($conn)){
                    Exceptions::error_rep("An error occured while creating an worktime entry. | SQL-Error: " . mysqli_error($conn));
                    error_log(mysqli_error($conn));
                    return false;
                } else {
                    return true;
                }
            }
        }

        public static function end_easymode_worktime($username, $id){
            $time = date("H:i");
            $conn = self::get_conn();
            $user = new Benutzer();

            if(!$user->get_user($username)){
                return false;
            } else {
                $sql = "UPDATE `arbeitszeiten` SET `schicht_ende` = '$time', `active` = '0' WHERE `id` = '{$id}';";
                mysqli_query($conn, $sql);
                if(mysqli_error($conn)){
                    Exceptions::error_rep("An error occured while creating an worktime entry. | SQL-Error: " . mysqli_error($conn));
                    error_log(mysqli_error($conn));
                    return false;
                } else {
                    return true;
                }
            }
        }

        public function start_easymode_pause_worktime($username, $id){
            $time = date("H:i");
            $conn = self::get_conn();
            $user = new Benutzer;

            if(!$user->get_user($username)){
                return false;
            } else {
                $sql = "UPDATE `arbeitszeiten` SET `pause_start` = '{$time}' WHERE id = '{$id}';";
                mysqli_query($conn, $sql);
                if(mysqli_error($conn)){
                    Exceptions::error_rep("An error occured while starting user pause for worktime with ID '{$id}' for user '{$username}'. Error: " . mysqli_error($conn));
                    return false;
                } else {
                    return true;
                }
            }
        }
        public function end_easymode_pause_worktime($username, $id){
            $time = date("H:i");
            $conn = self::get_conn();
            $user = new Benutzer;

            if(!$user->get_user($username)){
                return false;
            } else {
                $sql = "UPDATE `arbeitszeiten` SET `pause_end` = '{$time}' WHERE id = '{$id}';";
                mysqli_query($conn, $sql);
                if(mysqli_error($conn)){
                    Exceptions::error_rep("An error occured while ending user pause for worktime with ID '{$id}' for user '{$username}'. Error: " . mysqli_error($conn));
                    return false;
                } else {
                    return true;
                }
            }
        }

        public function toggle_easymode($username){
            $sql = "SELECT * FROM `users` WHERE username = '$username';";
            $res = mysqli_query(self::get_conn(), $sql);
            if(!$res){
                Exceptions::error_rep("An error occured while toggling easymode for user '{$username}'!");
                return false;
            } else {
                $data = mysqli_fetch_assoc($res);
                if($data["easymode"] == false){
                    $sql1 = "UPDATE `users` SET `easymode` = '1' WHERE username = '{$username}';";
                    $res1 = mysqli_query(self::get_conn(), $sql1);
                    if(!$res){
                        Exceptions::error_rep("An error occured while toggling easymode for user '{$username}'! Could not enable mode.");
                        return false;
                    }
                    return true;
                } else {
                    $sql1 = "UPDATE `users` SET `easymode` = '0';";
                    $res1 = mysqli_query(self::get_conn(), $sql1);
                    if(!$res){
                        Exceptions::error_rep("An error occured while toggling easymode for user '{$username}'! Could not disable mode.");
                        return false;
                    }
                    return true;
                }
            }
        }

        public function get_easymode_status($username, $mode = 0){
            $sql = "SELECT * FROM `users` WHERE username = '$username';";
            $res = mysqli_query(self::get_conn(), $sql);
            if(!$res){
                Exceptions::error_rep("An error occured while getting status easymode for user '{$username}'!");
                return false;
            } else {
                $data = mysqli_fetch_assoc($res);
                if($data["easymode"] == true){
                    if($mode == 1){
                        return true;
                    }
                    return "Easymode aktiviert.";
                } else {
                    if($mode == 1){
                        return false;
                    }
                    return "Easymode deaktiviert.";
                }
            }
        }

        /**
         * check_easymode_worktime_finished - Überprüft, ob ein Arbeitszeiteintrag im easymode AKTIV ist, sprich nicht beendet ist
         * 
         * Sollte es mehrere Einträge dazu geben, wird `false` zurückgegeben. Wenn keiner gefunden wird, wird der Wert `-1` zurückgegebem, um dem Handler bei der HTML-Auswahl zu sagen, dass keine aktive Zeit existiert.
         * Wenn ein Eintrag gefunden wird, wir die ID als int zurückgegeben
         *
         * @param string $username The username to check
         * @return false|int
         */
        public static function check_easymode_worktime_finished($username){
            $conn = Arbeitszeit::get_conn();
            $sql = "SELECT * FROM `arbeitszeiten` WHERE active = '1' AND username = '{$username}';";
            $res = mysqli_query($conn, $sql);
            if(mysqli_error($conn)){
                Exceptions::error_rep("An error occured while checking user entries for easymode worktime");
                return false;
            } else {
                if(mysqli_num_rows($res) > 1){
                    Exceptions::error_rep("An error occured while checking user entries for easymode worktime. Duplicated easymode entry found, please fix manually.");
                    return false;
                } elseif(mysqli_num_rows($res) < 1){
                    return -1;
                } else {
                    return (int) mysqli_fetch_assoc($res)["id"];
                }
            }
        }

        /**
         * add_worktime - Fügt einen Eintrag eines Mitarbeiters hinzu
         * 
         * @param string $start Start der Schicht
         * @param string $end Ende der Schicht
         * @param string $location Ort der Schicht
         * @param date $date Datum der Schicht
         * @param string $username Username des Mitarbeiters
         * @return bool Gibt true bei Erfolg und false bei einem Fehler zurück
         */
        public function add_worktime($start, $end, $location, $date, $username, $type, $pause = null, $meta = null){
            $conn = Arbeitszeit::get_conn();
            $user = new Benutzer;
            $usr = $user->get_user($username);
            if($date > date("y-m-d") /*|| $date < date("y-m-d")*/){
                return false;
            }
            if(!$user->get_user($username)){
                return false;
            } else {
                $sql = "INSERT INTO `arbeitszeiten` (`name`, `id`, `email`, `username`, `schicht_tag`, `schicht_anfang`, `schicht_ende`, `ort`, `review`, `active`, `type`, `pause_start`, `pause_end`, `attachements`) VALUES ( '{$usr["name"]}', '0', '{$usr["email"]}', '{$username}', '{$date}', '{$start}', '{$end}', '{$location}', '0', '0', '{$type}', '{$pause["start"]}', '{$pause["end"]}', '{$meta}');";
                mysqli_query($conn, $sql);
                if(mysqli_error($conn)){
                    Exceptions::error_rep("An error occured while creating an worktime entry. | SQL-Error: " . mysqli_error($conn));
                    error_log(mysqli_error($conn));
                    return false;
                } else {
                    return true;
                }
            }
        }

        /**
         * get_conn - Returns the database connection...
         * Similar to the constructor function
         * 
         * 
         */
        public static function get_conn(){
            $ini = self::get_app_ini()["mysql"];
            $conn = \mysqli_connect($ini["db_host"], $ini["db_user"], $ini["db_password"], $ini["db"]);
            mysqli_set_charset($conn, "utf8");
            if(mysqli_error($conn)){
                Exceptions::error_rep("An error occured while connecting to the database. | SQL-Error: " . mysqli_error($conn));
                return [
                    "error" => [
                        "error_code" => 0,
                        "error_message" => "Error while creating a connection to the database!"
                    ]
                ];
            } else {
                return $conn;
            }
        }
        /**
         * get_app_ini - Liest die Einstellungen aus der Datei "app.ini"
         * 
         * @return array Gibt ein Array mit den Einstellungen zurück
         */
        public static function get_app_ini(){
            $ini = parse_ini_file(dirname(__DIR__, 1) . "/inc/app.ini", true);
            return $ini;
        }

        /**
         * check_app_ini() - Überprüft app.ini Werte
         * 
         * @return array|bool Gibt ein Array mit Fehlern zurück, ansonsten true bei keinen Fehlern
         */
        public function check_app_ini(){
            $ini = $this->get_app_ini();
            foreach($ini as $key => $value){
                if(!isset($value)){
                    Exceptions::error_rep("An error occured while checking app.ini - One or more values are missing - $key unset!");
                    return [
                        "error_code" => 100,
                        "error_message" => "One or more of the values of the app.ini file are missing.\nDetailed Error: {$key} is unset!"
                    ];
                }

                if($key == "language"){
                    if($value != "de" || $value != "en"){
                        Exceptions::error_rep("An error occured while checking app.ini - language not defined correctly. choose either 'de' or 'en'!");
                        return [
                            "error_code" => 100,
                            "error_message" => "One or more values of the app.ini are missing or incorrect.\nDetailed Error: {$key} value can only store 'de' or 'en'!"
                        ];
                    }
                }
            }
            return true;

            
        }

        public function get_all_worktime(){

        }

        public function get_specific_worktime_html(int $month, int $year){
            $conn = Arbeitszeit::get_conn();
            $base_url = $ini = Arbeitszeit::get_app_ini()["general"]["base_url"];
                $sql = "SELECT * FROM `arbeitszeiten` WHERE YEAR(schicht_tag) = '{$year}' AND MONTH(schicht_tag) = '{$month}' ORDER BY schicht_tag DESC;";
                $res = mysqli_query($conn, $sql);
                if(mysqli_num_rows($res) > 0){
                    while($row = mysqli_fetch_assoc($res)){
                        $rnw = $row["name"];
                        $rnw2= $row["username"];
                        if(isset($year) || isset($month)){
                            $rrr = [
                                "jahr" => $year,
                                "monat" => $month
                            ];
                        } else {
                            $rrr = null;
                        }

                        $rrr2 = http_build_query($rrr, "\n");
                        $raw = strftime("%d.%m.%Y", strtotime($row["schicht_tag"]));
                        $rew = $row["schicht_anfang"];
                        $rol = $row["schicht_ende"];
                        $rum = $row["ort"];
                        $rqw = $row["id"]; # TODO: fix broken "delete" link
                        $rbn = $row["username"];
                        $rps = strftime("%H:%M", strtotime($row["pause_start"]));
                        $rpe = strftime("%H:%M", strtotime($row["pause_end"]));

                        if($rps == "01:00" || $rps == null){
                            $rps = "-";
                        }
                        if($rpe == "01:00" || $rpe == null){
                            $rpe = "-";
                        }

                        if($row["review"] == 0){
                            $rmm = "<a href=\"http://{$base_url}/suite/admin/actions/worktime/review.php?id={$rqw}&u={$rbn}\">zur Prüfung</a>";
                            $rno = null;
                        } else {
                            $rno = " <span style='color:red;'>&#9888; zur Prüfung &#9888;</span>" ?? null;
                            $rmm = "<a href=\"http://{$base_url}/suite/admin/actions/worktime/unlock.php?id={$rqw}&u={$rbn}\">Prüfung aufheben</a>";
                        }

                        $data = <<< DATA

                        <tr>
                            <td><a href="http://{$base_url}/suite/worktime/view_pdf.php?mitarbeiter={$rnw2}&{$rrr2}">(Drucken) $rnw</a>, <a href="http://{$base_url}/suite/admin/actions/worktime/delete.php?id={$rqw}&u={$rbn}">Eintrag löschen</a> oder $rmm</td>
                            <td>$raw</td>
                            <td>$rew</td>
                            <td>$rol</td>
                            <td>$rps</td>
                            <td>$rpe</td>
                            <td>$rum $rno</td>
                        </tr>


                        DATA;


                        	echo $data;
                        
                    }
                } else {
                    return "Keine Schichten eingetragen.";
                }
            
        }
        public function get_employee_worktime_html($username){
            $conn = Arbeitszeit::get_conn();
            $base_url = $ini = Arbeitszeit::get_app_ini()["general"]["base_url"];
            $sql = "SELECT * FROM `arbeitszeiten` WHERE username = '{$username}' ORDER BY id DESC;";
            $res = mysqli_query($conn, $sql);
            if($res == false){
                return "Keine Schichten eingetragen.";
            }
            if(mysqli_num_rows($res) > 0){
                while($row = mysqli_fetch_assoc($res)){
                    $rnw = $row["name"];

                    $raw = strftime("%d.%m.%Y", strtotime($row["schicht_tag"]));
                    $rew = $row["schicht_anfang"];
                    $rol = $row["schicht_ende"];
                    $rum = $row["ort"];
                    $rqw = $row["id"];
                    $rps = strftime("%H:%M", strtotime($row["pause_start"]));
                    $rpe = strftime("%H.%M", strtotime($row["pause_end"]));

                    if($rps == "01:00" || $rps == null){
                        $rps = "-";
                    }
                    if($rpe == "01:00" || $rpe == null){
                        $rpe = "-";
                    }

                    if($row["review"] != 0){
                        $rmn = "style='color: red;'" ?? null;
                        $rno = "&#9888; zur Prüfung. &#9888;" ?? null;
                    } else {
                        $rmn = null;
                        $rno = null;
                    }
                    $data = <<< DATA

                        <tr>
                            <td $rmn>$raw</td>
                            <td $rmn>$rew</td>
                            <td $rmn>$rol</td>
                            <td>$rps</td>
                            <td>$rpe</td>
                            <td $rmn>$rum $rno</td>
                        </tr>


                        DATA;



                        echo $data;  
                }
            } else {
                return "Keine Schichten eingetragen.";
            }
        }

        public function mark_for_review($id){
            $conn = Arbeitszeit::get_conn();
            $sql = "UPDATE `arbeitszeiten` SET `review` = '1' WHERE `id` = '{$id}';";
            $res = mysqli_query($conn, $sql);
            if($res == false){
                Exceptions::error_rep("An error occured while marking an worktime as under review, id '{$id}'. SQL-Error: " . mysqli_error(self::get_conn()));
                return false;
            } else {
                return true;
            }
        }

        public function unlock_for_review($id){
            $conn = Arbeitszeit::get_conn();
            $sql = "UPDATE `arbeitszeiten` SET `review` = '0' WHERE `id` = '{$id}';";
            $res = mysqli_query($conn, $sql);
            if($res == false){
                Exceptions::error_rep("An error occured while unlocking an worktime from review, id '{$id}'. SQL-Error: " . mysqli_error(Arbeitszeit::get_conn()));
                return false;
            } else {
                return true;
            }
        }

        public function check_status_code($url){
                if(strpos($url, "info=worktime_deleted")){
                    return "<p><span style='color:blue;'>Hinweis: Arbeitszeit erfolgreich gelöscht!</p>";
                }
                if(strpos($url, "info=logged_out")){
                    return "<p><span style='color:blue;'>Hinweis: Erfolgreich ausgeloggt!</p>";
                }
                if(strpos($url, "info=error_sickness")){
                    return "<p><span style='color:red;'>Fehler: Deine Krankheit konnte nicht gespeichert werden! Bitte kontaktiere deinen Administrator!</p>";
                }
                if(strpos($url, "info=error_vacation")){
                    return "<p><span style='color:red;'>Fehler: Dein Urlaub konnte nicht gespeichert werden! Bitte kontaktiere deinen Administrator</p>";
                }
                if(strpos($url, "info=vacation_added")){
                    return "<p><span style='color:green;'>Hinweis: Dein Urlaub wurde erfolgreich gespeichert!</p>";
                }
                if(strpos($url, "info=sickness_added")){
                    return "<p><span style='color:green;'>Hinweis: Deine Krankheit wurde erfolgreich gespeichert!</p>";
                }
                if(strpos($url, "info=calendar_entry_deleted")){
                    return "<p><span style='color:blue;'>Hinweis: Kalendereintrag gelöscht!</p>";
                }
                if(strpos($url, "info=noperms")){
                    return "<p><span style='color:red;'>Hinweis: Fehlende Berechtigungen!</p>";
                }
                if(strpos($url, "info=user_deleted")){
                    return "<p><span style='color:blue;'>Hinweis: Benutzer erfolgreich gelöscht!</p>";
                }
                if(strpos($url, "info=created_user")){
                    return "<p><span style='color:green;'>Hinweis: Benutzer erfolgreich erstellt!</p>";
                }
                if(strpos($url, "info=worktime_added")){
                    return "<p><span style='color:green;'>Hinweis: Arbeitszeit erfolgreich hinzugefügt!</span></p>";
                }
                if(strpos($url, "info=mailbox_entry_added")){
                    return "<p><span style='color: green;'>Hinweis: Mailbox-Eintrag hinzugefügt!</span></p>";
                }
                if(strpos($url, "info=password_changed")){
                    return "<p><span style='color: green;'>Hinweis: Dein Passwort wurde erfolgreich geändert. Bitte melde dich erneut an.</span></p>";
                }
                if(strpos($url, "info=password_change_failed")){
                    return "<p><span style='color: red;'>Fehler: Dein Passwort konnte nicht geändert werden, bitte kontaktiere den Support.</span></p>";
                }
                if(strpos($url, "error=nodata")){
                    return "<p><span style='color: red;'>Fehler: Es wurden kein Benutzername oder Passwort eingegeben.</span></p>";
                }
                if(strpos($url, "info=statemismatch")){
                    return "<p><span style='color: red;'>Fehler: Sicherheitsfehler.</span></p>";
                }
                if(strpos($url, "info=wrongdata")){
                    return "<p><span style='color: red;'>Fehler: Falsche Anmeldedaten.</span></p>";
                }
                if(strpos($url, "info=worktime_review")){
                    return "<p><span style='color:blue;'>Hinweis: Arbeitszeit erfolgreich auf Prüfung gestellt!</span></p>";
                }
                if(strpos($url, "info=worktime_review_unlock")){
                    return "<p><span style='color:blue;'>Hinweis: Prüfung erfolgreich aufgehoben für Arbeitszeit!</span></p>";
                }
                if(strpos($url, "info=worktime_easymode_start")){
                    return "<p><span style='color:blue;'>Hinweis: Deine Schicht wurde erfolgreich gestartet!</span></p>";
                }
                if(strpos($url, "info=worktime_easymode_end")){
                    return "<p><span style='color:blue;'>Hinweis: Deine Schicht wurde erfolgreich beendet!</span></p>";
                }
                if(strpos($url, "info=worktime_easymode_pause_start")){
                    return "<p><span style='color:blue;'>Hinweis: Deine Pause wurde erfolgreich gestartet!</span></p>";
                }
                if(strpos($url, "info=worktime_easymode_pause_end")){
                    return "<p><span style='color:blue;'>Hinweis: Deine Pause wurde erfolgreich beendet!</span></p>";
                }
                if(strpos($url, "info=easymode_toggled")){
                    return "<p><span style='color:blue;'>Hinweis: Der Modus wurde erfolgreich gewechselt!</span></p>";
                }
                if(strpos($url, "info=license_error")){
                    return "<p><span style='color:red;'>Hinweis: Entweder ist die Lizenz ungültig oder die maximale Anzahl an Benutzer wurde erreicht. Bitte upgraden.</span></p>";
                }
                if(strpos($url, "info=error")){
                    return "<p><span style='color:red;'>Hinweis: Ein Fehler ist aufgetreten. Bitte überprüfe den Log, falls möglich.</span></p>";
                }
        
        }

        public function calculate_hours_specific_time($username, $month, $year){
            $conn = Arbeitszeit::get_conn();
            $sql = "SELECT * FROM `arbeitszeiten` WHERE `username` = '{$username}' AND MONTH(schicht_tag) = '{$month}' AND YEAR(schicht_tag) = '{$year}' ORDER BY `schicht_tag` DESC;";
            $res = mysqli_query($conn, $sql);
            if(mysqli_num_rows($res) > 0){
                $hours = 0;
                while($worktime = mysqli_fetch_assoc($res)){
                    $start = strtotime($worktime["schicht_anfang"]);
                    $end = strtotime($worktime["schicht_ende"]);
                    $hours += ($end - $start)/3600;
                }
                return $r = [
                    "hours_rounded" => round($hours, 2, PHP_ROUND_HALF_UP),
                    "hours_raw" => $hours
                ];
                
            }
        }

        /**
         * change_settings() - Allows you to change specific settings in the app.ini
         * 
         * @param array $array Contains the array with changing values
         * @return bool Returns true o success and false otherwise
         */
        public static function change_settings($array){
            $ini = self::get_app_ini();
            foreach($array as $key => $value){
                    unset($ini["general"][(string) $key]);
                    $ini["general"][(string) $key] = $value;

            }
            $file = fopen($_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/app.ini", "w");
            $cini = self::arr2ini($ini);
            if(fwrite($file, $cini)){
                fclose($file);
                return true;
            } else {
                Exceptions::error_rep("An error occured while chaning settings");
                fclose($file);
                return false;
            }
        }

        public static function arr2ini(array $a, array $parent = array()){
            $out = '';
            foreach ($a as $k => $v){
                if (is_array($v)){
                //subsection case
                //merge all the sections into one array...
                $sec = array_merge((array) $parent, (array) $k);
                //add section information to the output
                $out .= '[' . join('.', $sec) . ']' . PHP_EOL;
                //recursively traverse deeper
                $out .= self::arr2ini($v, $sec);
                } else {
                    //plain key->value case
                    $out .= "$k=\"$v\"" . PHP_EOL;
                }
            }
            return $out;
        }

        public static function get_worktime_by_id($id){
            $conn = self::get_conn();
            $sql = "SELECT * FROM `arbeitszeiten` WHERE id = '{$id}';";
            $res = mysqli_query($conn, $sql);
            if(!$res){
                return false;
            } else {
                $data = mysqli_fetch_assoc($res);
                return $data;
            }
        }
    
    }
}



?>