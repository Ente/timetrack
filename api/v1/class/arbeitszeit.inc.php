<?php
namespace Arbeitszeit {
    use Arbeitszeit\DB;
    use Arbeitszeit\Kalender;
    use Arbeitszeit\i18n;
    use Arbeitszeit\Benutzer;
    use Arbeitszeit\Auth;
    use Arbeitszeit\pdf;
    use Arbeitszeit\Mode;
    use Arbeitszeit\Autodelete;
    use Arbeitszeit\Exceptions;
    use Arbeitszeit\Vacation;
    use Arbeitszeit\Sickness;
    /**
     * Beinhaltet wesentliche Inhalte, wie Einstellungen, Arbeitszeiten erstellen, etc.
     * 
     * @author Bryan Böhnke-Avan <bryan@duckerz.de>
     */
    class Arbeitszeit
    {

        private $db;
        private $kalender;
        private $i18nC;
        private $i18n;
        private $benutzer;
        private $auth;
        private $pdf;
        private $mode;
        private $autodelete;
        private $sickness;
        private $vacation;

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

        public function __construct()
        {
            $this->db = new DB();
            if (self::get_app_ini()["general"]["debug"] == true || self::get_app_ini()["general"]["debug"] == "false") {
                #_errors", 1);
            }
            $this->init_lang() ?? null;
        }

        public function init_lang(){
            $n = new i18n;
            $this->i18n = $n->loadLanguage(null, "class/arbeitszeit");
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
        public function delete_worktime($id)
        {
            $data = $this->db->sendQuery("DELETE FROM arbeitszeiten WHERE id = ?")->execute([$id]);
            if ($data == false) {
                Exceptions::error_rep("An error occured while deleting an worktime entry. See previous message for more information.");
                return [
                    "error" => [
                        "error_code" => 1,
                        "error_message" => "Error while deleting an entry from the database!"
                    ]
                ];
            } else {
                return 1;
            }

        }

        public static function add_easymode_worktime($username)
        {
            $date = date("Y-m-d");
            $time = date("H:i");
            $conn = new DB;
            $user = new Benutzer();
            $usr = $user->get_user($username);

            if (!$user->get_user($username)) {
                return false;
            } else {
                $sql = "INSERT INTO `arbeitszeiten` (`name`, `id`, `email`, `username`, `schicht_tag`, `schicht_anfang`, `schicht_ende`, `ort`, `active`, `review`) VALUES ( ?, '0', ?, ?, ?, ?, '00:00', '-', '1', '0');";
                $data = $conn->sendQuery($sql)->execute([$usr["name"], $usr["email"], $username, $date, $time]);
                if ($data == false) {
                    Exceptions::error_rep("An error occured while creating an worktime entry. See previous message for more information");
                    return false;
                } else {
                    return true;
                }
            }
        }

        public static function end_easymode_worktime($username, $id)
        {
            $time = date("H:i");
            $conn = new DB;
            $user = new Benutzer();

            if (!$user->get_user($username)) {
                return false;
            } else {
                $sql = "UPDATE `arbeitszeiten` SET `schicht_ende` = ?, `active` = '0' WHERE `id` = ?;";
                $data = $conn->sendQuery($sql)->execute([$time, $id]);
                if (!$data) {
                    Exceptions::error_rep("An error occured while creating an worktime entry. See previous message for more information.");
                    return false;
                } else {
                    return true;
                }
            }
        }

        public function start_easymode_pause_worktime($username, $id)
        {
            $time = date("H:i");
            $user = new Benutzer;

            if (!$user->get_user($username)) {
                return false;
            } else {
                $sql = "UPDATE `arbeitszeiten` SET `pause_start` = ? WHERE id = ?;";
                $data = $this->db->sendQuery($sql)->execute([$time, $id]);
                if (!$data) {
                    Exceptions::error_rep("An error occured while starting user pause for worktime with ID '{$id}' for user '{$username}'. See previous message for more information.");
                    return false;
                } else {
                    return true;
                }
            }
        }
        public function end_easymode_pause_worktime($username, $id)
        {
            $time = date("H:i");
            $user = new Benutzer;

            if (!$user->get_user($username)) {
                return false;
            } else {
                $sql = "UPDATE `arbeitszeiten` SET `pause_end` = ? WHERE id = ?;";
                $data = $this->db->sendQuery($sql)->execute([$time, $id]);
                if (!$data) {
                    Exceptions::error_rep("An error occured while ending user pause for worktime with ID '{$id}' for user '{$username}'. See previous message for more information.");
                    return false;
                } else {
                    return true;
                }
            }
        }

        public function toggle_easymode($username)
        {
            $sql = "SELECT * FROM `users` WHERE username = ?;";
            $res = $this->db->sendQuery($sql);
            $res->execute([$username]);
            if (!$res) {
                Exceptions::error_rep("An error occured while toggling easymode for user '{$username}'!");
                return false;
            } else {
                $data = $res->fetch(\PDO::FETCH_ASSOC);
                if ($data["easymode"] == false) {
                    $sql1 = "UPDATE `users` SET `easymode` = '1' WHERE username = ?;";
                    $res1 = $this->db->sendQuery($sql1)->execute([$username]);
                    if (!$res1) {
                        Exceptions::error_rep("An error occured while toggling easymode for user '{$username}'! Could not enable mode.");
                        return false;
                    }
                    return true;
                } else {
                    $sql1 = "UPDATE `users` SET `easymode` = '0';";
                    $res1 = $this->db->sendQuery($sql)->execute();
                    if (!$res1) {
                        Exceptions::error_rep("An error occured while toggling easymode for user '{$username}'! Could not disable mode.");
                        return false;
                    }
                    return true;
                }
            }
        }

        public function get_easymode_status($username, $mode = 0)
        {
            $sql = "SELECT * FROM `users` WHERE username = ?;";
            $res = $this->db->sendQuery($sql);
            $res->execute([$username]);
            if (!$res) {
                Exceptions::error_rep("An error occured while getting status easymode for user '{$username}'!");
                return false;
            } else {
                $data = $res->fetch(\PDO::FETCH_ASSOC);
                if ($data["easymode"] == true) {
                    if ($mode == 1) {
                        return true;
                    }
                    return "{$this->i18n["easymode_enabled"]}";
                } else {
                    if ($mode == 1) {
                        return false;
                    }
                    return "{$this->i18n["easymode_disabled"]}";
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
        public static function check_easymode_worktime_finished($username)
        {
            $db = new DB;
            $sql = "SELECT * FROM `arbeitszeiten` WHERE active = '1' AND username = ?;";
            $res = $db->sendQuery($sql);
            $res->execute([$username]);
            if (!$res) {
                Exceptions::error_rep("An error occured while checking user entries for easymode worktime");
                return false;
            } else {
                if ($res->rowCount() > 1) {
                    Exceptions::error_rep("An error occured while checking user entries for easymode worktime. Duplicated easymode entry found, please fix manually.");
                    return false;
                } elseif ($res->rowCount() < 1) {
                    return -1;
                } else {
                    return (int) $res->fetch(\PDO::FETCH_ASSOC)["id"];
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
        public function add_worktime($start, $end, $location, $date, $username, $type, $pause = null, $meta = null)
        {
            $user = new Benutzer;
            $usr = $user->get_user($username);
            if ($date > date("y-m-d") /*|| $date < date("y-m-d")*/) {
                return false;
            }
            if (!$user->get_user($username)) {
                return false;
            } else {
                $sql = "INSERT INTO `arbeitszeiten` (`name`, `id`, `email`, `username`, `schicht_tag`, `schicht_anfang`, `schicht_ende`, `ort`, `review`, `active`, `type`, `pause_start`, `pause_end`, `attachements`) VALUES ( ?, '0', ?, ?, ?, ?, ?, ?, '0', '0', ?, ?, ?, ?);";
                $data = $this->db->sendQuery($sql);
                $data->execute([$usr["name"], $usr["email"], $username, $date, $start, $end, $location, $type, $pause["start"], $pause["end"], $meta]);
                if (!$data) {
                    Exceptions::error_rep("An error occured while creating an worktime entry. See previous message for more information.");
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
        public static function get_conn()
        {
            $ini = self::get_app_ini()["mysql"];
            $conn = \mysqli_connect($ini["db_host"], $ini["db_user"], $ini["db_password"], $ini["db"]);
            mysqli_set_charset($conn, "utf8");
            if (mysqli_error($conn)) {
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
        public static function get_app_ini()
        {
            $ini = parse_ini_file($_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/app.ini", true);
            return $ini;
        }

        /**
         * check_app_ini() - Überprüft app.ini Werte
         * 
         * @return array|bool Gibt ein Array mit Fehlern zurück, ansonsten true bei keinen Fehlern
         */
        public function check_app_ini()
        {
            $ini = $this->get_app_ini();
            foreach ($ini as $key => $value) {
                if (!isset($value)) {
                    Exceptions::error_rep("An error occured while checking app.ini - One or more values are missing - $key unset!");
                    return [
                        "error_code" => 100,
                        "error_message" => "One or more of the values of the app.ini file are missing.\nDetailed Error: {$key} is unset!"
                    ];
                }

                if ($key == "language") {
                    if ($value != "de" || $value != "en") {
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

        public function get_all_worktime()
        {
            $sql = "SELECT * FROM `arbeitszeiten`;";
            $res = $this->db->sendQuery($sql);
            $res->execute();
            $arr = [];
            if($res->rowCount() == 0){
                return false;
            }
            while($row = $res->fetch(\PDO::FETCH_ASSOC)){
                $arr[$row["id"]] = $row;
            }
            return $arr;
        }

        public function get_all_user_worktime($username)
        {
            $sql = "SELECT * FROM `arbeitszeiten` WHERE username = ?;";
            $res = $this->db->sendQuery($sql);
            $res->execute([$username]);
            $arr = [];
            if($res->rowCount() == 0){
                return false;
            }
            while($row = $res->fetch(\PDO::FETCH_ASSOC)){
                $arr[$row["id"]] = $row;
            }
            return $arr;
        }

        public function get_specific_worktime_html(int $month, int $year)
        {
            $base_url = $ini = Arbeitszeit::get_app_ini()["general"]["base_url"];
            $sql = "SELECT * FROM `arbeitszeiten` WHERE YEAR(schicht_tag) = ? AND MONTH(schicht_tag) = ? ORDER BY schicht_tag DESC;";
            $res = $this->db->sendQuery($sql);
            $res->execute([$year, $month]);
            if ($res->rowCount() > 0) {
                while ($row = $res->fetch(\PDO::FETCH_ASSOC)) {
                    $rnw = $row["name"];
                    $rnw2 = $row["username"];
                    if (isset($year) || isset($month)) {
                        $rrr = [
                            "jahr" => $year,
                            "monat" => $month
                        ];
                    } else {
                        $rrr = null;
                    }

                    $rrr2 = http_build_query($rrr, "\n");
                    $raw = @strftime("%d.%m.%Y", strtotime($row["schicht_tag"]));
                    $rew = $row["schicht_anfang"];
                    $rol = $row["schicht_ende"];
                    $rum = $row["ort"];
                    $rqw = $row["id"]; # TODO: fix broken "delete" link
                    $rbn = $row["username"];
                    $rps = @strftime("%H:%M", strtotime($row["pause_start"]));
                    $rpe = @strftime("%H:%M", strtotime($row["pause_end"]));

                    if ($rps == "01:00" || $rps == null) {
                        $rps = "-";
                    }
                    if ($rpe == "01:00" || $rpe == null) {
                        $rpe = "-";
                    }

                    if ($row["review"] == 0) {
                        $rmm = "<a href=\"http://{$base_url}/suite/admin/actions/worktime/review.php?id={$rqw}&u={$rbn}\">{$this->i18n["to_review"]}</a>";
                        $rno = null;
                    } else {
                        $rno = " <span style='color:red;'>&#9888; {$this->i18n["to_review"]} &#9888;</span>" ?? null;
                        $rmm = "<a href=\"http://{$base_url}/suite/admin/actions/worktime/unlock.php?id={$rqw}&u={$rbn}\">{$this->i18n["remove_review"]}</a>";
                    }

                    $data = <<< DATA

                        <tr>
                            <td><a href="http://{$base_url}/suite/worktime/view_pdf.php?mitarbeiter={$rnw2}&{$rrr2}" target="_blank">{$this->i18n["print"]} $rnw</a>, <a href="http://{$base_url}/suite/admin/actions/worktime/delete.php?id={$rqw}&u={$rbn}">{$this->i18n["delete_entry"]}</a> {$this->i18n["or"]} $rmm</td>
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
                return "{$this->i18n["no_shifts"]}";
            }

        }
        public function get_employee_worktime_html($username)
        {
            $base_url = $ini = Arbeitszeit::get_app_ini()["general"]["base_url"];
            $sql = "SELECT * FROM `arbeitszeiten` WHERE username = ? ORDER BY id DESC;";
            $res = $this->db->sendQuery($sql);
            $res->execute([$username]);
            if ($res == false) {
                return "No shifts found.";
            }
            if ($res->rowCount() > 0) {
                while ($row = $res->fetch(\PDO::FETCH_ASSOC)) {
                    $rnw = $row["name"];

                    $raw = @strftime("%d.%m.%Y", strtotime($row["schicht_tag"]));
                    $rew = $row["schicht_anfang"];
                    $rol = $row["schicht_ende"];
                    $rum = $row["ort"];
                    $rqw = $row["id"];
                    $rps = @strftime("%H:%M", strtotime($row["pause_start"]));
                    $rpe = @strftime("%H.%M", strtotime($row["pause_end"]));

                    if ($rps == "01:00" || $rps == null) {
                        $rps = "-";
                    }
                    if ($rpe == "01:00" || $rpe == null) {
                        $rpe = "-";
                    }

                    if ($row["review"] != 0) {
                        $rmn = "style='color: red;'" ?? null;
                        $rno = "&#9888; {$this->i18n["to_review"]} &#9888;" ?? null;
                    } else {
                        $rmn = null;
                        $rno = null;
                    }
                    $data = <<<DATA

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
                return "{$this->i18n["no_shifts"]}";
            }
        }

        public function mark_for_review($id)
        {
            $sql = "UPDATE `arbeitszeiten` SET `review` = '1' WHERE `id` = ?;";
            $res = $this->db->sendQuery($sql)->execute([$id]);
            if (!$res) {
                Exceptions::error_rep("An error occured while marking an worktime as under review, id '{$id}'. See previous message for more information.");
                return false;
            } else {
                return true;
            }
        }

        public function unlock_for_review($id)
        {
            $sql = "UPDATE `arbeitszeiten` SET `review` = '0' WHERE `id` = ?;";
            $res = $this->db->sendQuery($sql)->execute([$id]);
            if (!$res) {
                Exceptions::error_rep("An error occured while unlocking an worktime from review, id '{$id}'. See previous message for more information.");
                return false;
            } else {
                return true;
            }
        }

        public static function check_status_code($url)
        {
            $i18n = new i18n;
            $loc = $i18n->loadLanguage(null, "status");
            if (strpos($url, "info=worktime_deleted")) {
                return "<p><span style='color:blue;'>{$loc["worktime_deleted"]}</p>";
            }
            if (strpos($url, "info=logged_out")) {
                return "<p><span style='color:blue;'>{$loc["logged_out"]}</p>";
            }
            if (strpos($url, "info=error_sickness")) {
                return "<p><span style='color:red;'>{$loc["error_sickness"]}</p>";
            }
            if (strpos($url, "info=error_vacation")) {
                return "<p><span style='color:red;'>{$loc["error_vacation"]}</p>";
            }
            if (strpos($url, "info=vacation_added")) {
                return "<p><span style='color:green;'>{$loc["vacation_added"]}</p>";
            }
            if (strpos($url, "info=password_reset")) {
                return "<p><span style='color:green;'>{$loc["password_reset"]}</p>";
            }
            if (strpos($url, "info=sickness_added")) {
                return "<p><span style='color:green;'>{$loc["sickness_added"]}</p>";
            }
            if (strpos($url, "info=calendar_entry_deleted")) {
                return "<p><span style='color:blue;'>{$loc["calendar_entry_deleted"]}</p>";
            }
            if (strpos($url, "info=noperms")) {
                return "<p><span style='color:red;'>{$loc["noperms"]}</p>";
            }
            if (strpos($url, "info=user_deleted")) {
                return "<p><span style='color:blue;'>{$loc["user_deleted"]}</p>";
            }
            if (strpos($url, "info=created_user")) {
                return "<p><span style='color:green;'>{$loc["created_user"]}</p>";
            }
            if (strpos($url, "info=worktime_added")) {
                return "<p><span style='color:green;'>{$loc["worktime_added"]}</span></p>";
            }
            if (strpos($url, "info=password_changed")) {
                return "<p><span style='color: green;'>{$loc["password_changed"]}</span></p>";
            }
            if (strpos($url, "info=password_change_failed")) {
                return "<p><span style='color: red;'>{$loc["password_change_failed"]}</span></p>";
            }
            if (strpos($url, "error=nodata")) {
                return "<p><span style='color: red;'>{$loc["nodata"]}</span></p>";
            }
            if (strpos($url, "info=statemismatch")) {
                return "<p><span style='color: red;'>{$loc["statemismatch"]}</span></p>";
            }
            if (strpos($url, "error=wrongdata")) {
                return "<p><span style='color: red;'>{$loc["wrongdata"]}</span></p>";
            }
            if (strpos($url, "error=ldapauth")) {
                return "<p><span style='color: red;'>{$loc["ldapauth"]}</span></p>";
            }
            if (strpos($url, "info=ldapcreated")) {
                return "<p><span style='color: red;'>{$loc["ldapcreated"]}</span></p>";
            }
            if (strpos($url, "info=worktime_review")) {
                return "<p><span style='color:blue;'>{$loc["worktime_review"]}</span></p>";
            }
            if (strpos($url, "info=worktime_review_unlock")) {
                return "<p><span style='color:blue;'>{$loc["worktime_review_unlock"]}</span></p>";
            }
            if (strpos($url, "info=worktime_easymode_start")) {
                return "<p><span style='color:blue;'>{$loc["worktime_easymode_start"]}</span></p>";
            }
            if (strpos($url, "info=worktime_easymode_end")) {
                return "<p><span style='color:blue;'>{$loc["worktime_easymode_end"]}</span></p>";
            }
            if (strpos($url, "info=worktime_easymode_pause_start")) {
                return "<p><span style='color:blue;'>{$loc["worktime_easymode_pause_start"]}</span></p>";
            }
            if (strpos($url, "info=worktime_easymode_pause_end")) {
                return "<p><span style='color:blue;'>{$loc["worktime_easymode_pause_end"]}</span></p>";
            }
            if (strpos($url, "info=easymode_toggled")) {
                return "<p><span style='color:blue;'>{$loc["easymode_toggled"]}</span></p>";
            }
            if (strpos($url, "info=error")) {
                return "<p><span style='color:red;'>{$loc["error"]}</span></p>";
            }

        }

        public function calculate_hours_specific_time($username, $month, $year)
        {
            $sql = "SELECT * FROM `arbeitszeiten` WHERE `username` = ? AND MONTH(schicht_tag) = ? AND YEAR(schicht_tag) = ? ORDER BY `schicht_tag` DESC;";
            $res = $this->db->sendQuery($sql);
            $res->execute([$username, $month, $year]);
            if ($res->rowCount() > 0) {
                $hours = 0;
                while ($worktime = $res->fetch(\PDO::FETCH_ASSOC)) {
                    $start = strtotime($worktime["schicht_anfang"]);
                    $end = strtotime($worktime["schicht_ende"]);
                    $hours += ($end - $start) / 3600;
                }
                return $r = [
                    "hours_rounded" => round($hours, 1, PHP_ROUND_HALF_UP),
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
        public static function change_settings($array)
        {
            $ini = self::get_app_ini();
            foreach ($array as $key => $value) {
                unset($ini["general"][(string) $key]);
                $ini["general"][(string) $key] = $value;

            }
            $file = fopen($_SERVER["DOCUMENT_ROOT"] . "/api/v1/inc/app.ini", "w");
            $cini = self::arr2ini($ini);
            if (fwrite($file, $cini)) {
                fclose($file);
                return true;
            } else {
                Exceptions::error_rep("An error occured while chaning settings");
                fclose($file);
                return false;
            }
        }

        private static function arr2ini(array $a, array $parent = array())
        {
            $out = '';
            foreach ($a as $k => $v) {
                if (is_array($v)) {
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

        public static function get_worktime_by_id($id)
        {
            $conn = new DB;
            $sql = "SELECT * FROM `arbeitszeiten` WHERE id = ?;";
            $res = $conn->sendQuery($sql);
            $res->execute([$id]);
            if (!$res) {
                return false;
            } else {
                $data = $res->fetch(\PDO::FETCH_ASSOC);
                return $data;
            }
        }

        public function kalender(): Kalender{
            if(!$this->kalender) $this->kalender = new Kalender;
            return $this->kalender;
        }

        public function db(): DB{
            if(!$this->db) $this->db = new DB;
            return $this->db;
        }

        public function i18n(): i18n{
            if(!$this->i18nC) $this->i18nC = new i18n;
            return $this->i18nC;
        }

        public function benutzer(): Benutzer{
            if(!$this->benutzer) $this->benutzer = new Benutzer;
            return $this->benutzer;
        }

        public function auth(): Auth{
            if(!$this->auth) $this->auth = new Auth;
            return $this->auth;
        }

        public function pdf(): pdf{
            if(!$this->pdf) $this->pdf = new pdf;
            return $this->pdf;
        }

        public function mode(): Mode{
            if(!$this->mode) $this->mode = new Mode;
            return $this->mode;
        }

        public function autodelete(): Autodelete{
            if(!$this->autodelete) $this->autodelete = new Autodelete;
            return $this->autodelete;
        }

        public function sickness(): Sickness{
            if(!$this->sickness) $this->sickness = new Sickness;
            return $this->sickness;
        }

        public function vacation(): Vacation{
            if(!$this->vacation) $this->vacation = new Vacation;
            return $this->vacation;
        }
    }
}



?>