<?php
namespace Arbeitszeit {
    use Arbeitszeit\DB;
    use Arbeitszeit\Notifications;
    use Arbeitszeit\i18n;
    use Arbeitszeit\Benutzer;
    use Arbeitszeit\Auth;
    use Arbeitszeit\Mode;
    use Arbeitszeit\Autodelete;
    use Arbeitszeit\Exceptions;
    use Arbeitszeit\Vacation;
    use Arbeitszeit\Sickness;
    use Arbeitszeit\ExportModule;
    use Arbeitszeit\Mails;
    use Arbeitszeit\Nodes;
    use Arbeitszeit\Projects;
    use Arbeitszeit\StatusMessages;
    use Arbeitszeit\Events\EventDispatcherService;
    use Arbeitszeit\Events\EasymodeWorktimeAddedEvent; // "EasymodeWorktimeSTARTED" Event, actually.
    use Arbeitszeit\Events\EasymodeWorktimeEndedEvent;
    use Arbeitszeit\Events\EasymodeWorktimePauseEndEvent;
    use Arbeitszeit\Events\EasymodeWorktimePauseStartEvent;
    use Arbeitszeit\Events\WorktimeAddedEvent;
    use Arbeitszeit\Events\WorktimeDeletedEvent;
    use Arbeitszeit\Events\WorktimeMarkedForReviewEvent;
    use Arbeitszeit\Events\WorktimeUnlockedFromReviewEvent;
    use Arbeitszeit\Events\FixEasymodeWorktimeEvent;
    /**
     * Beinhaltet wesentliche Inhalte, wie Einstellungen, Arbeitszeiten erstellen, etc.
     * 
     * @author Bryan Böhnke-Avan <bryan@duckerz.de>
     */
    class Arbeitszeit
    {

        private $db;
        private $notifications;
        private $i18nC;
        private $i18n;
        private $benutzer;
        private $auth;
        private $mode;
        private $autodelete;
        private $sickness;
        private $vacation;
        private $exportModule;
        private $mails;
        private $nodes;
        private $statusMessages;

        private $projects;

        public function __construct()
        {
            $this->db = new DB();
            $this->init_lang() ?? null;
            if (isset($this->get_app_ini()["general"]["timezone"])) {
                try {
                    date_default_timezone_set($this->get_app_ini()["general"]["timezone"]);
                } catch (\Exception $e) {
                    Exceptions::error_rep("Error setting timezone: " . $e->getMessage());
                }
            }
        }

        public function __destruct()
        {
            if (filter_var(self::get_app_ini()["general"]["debug"], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) == true) {
                Exceptions::error_rep("Destroying Arbeitszeit class, dump of all loaded files: " . json_encode(get_included_files(), JSON_PRETTY_PRINT));
            }
        }

        public function init_lang()
        {
            Exceptions::error_rep("Initializing language for Arbeitszeit class");
            $n = new i18n;
            $this->i18n = $n->loadLanguage(null, "class/arbeitszeit");
        }


        /**
         * delete_worktime - Löscht einen Eintrag eines Mitarbeiters
         * 
         * @param int $id ID des zu löschenden Eintrags
         * @return bool|array Wirft "true" bei erfolg und ein Array bei einem Fehler zurück
         * 
         */
        public function delete_worktime($id)
        {
            if (!$this->nodes()->checkNode("arbeitszeit.inc", "delete_worktime")) {
                return false;
            }
            $data = $this->db->sendQuery("DELETE FROM arbeitszeiten WHERE id = ?")->execute([$id]);
            if ($data == false) {
                Exceptions::error_rep("An error occurred while deleting an worktime entry. See previous message for more information.");
                return [
                    "error" => [
                        "error_code" => 1,
                        "error_message" => "Error while deleting an entry from the database!"
                    ]
                ];
            } else {
                EventDispatcherService::get()->dispatch(new WorktimeDeletedEvent($_SESSION["username"], (int) $id), WorktimeDeletedEvent::NAME);
                Exceptions::error_rep("Worktime entry with ID '{$id}' deleted successfully.");
                return 1;
            }

        }

        public static function add_easymode_worktime($username)
        {
            $nodes = new Nodes;
            if (!$nodes->checkNode("arbeitszeit.inc", "add_easymode_worktime")) {
                return false;
            }
            Exceptions::error_rep("Creating easymode worktime entry for user '{$username}'...");
            $date = date("Y-m-d");
            $time = date("H:i");
            $conn = new DB;
            $user = new Benutzer();
            $usr = $user->get_user($username);

            if (!$user->get_user($username)) {
                Exceptions::error_rep("An error occurred while creating easymode worktime entry for user '{$username}'. User does not exist.");
                return false;
            } else {
                Exceptions::error_rep("Creating easymode worktime entry for user '{$username}'...");
                $sql = "INSERT INTO `arbeitszeiten` (`name`, `id`, `email`, `username`, `schicht_tag`, `schicht_anfang`, `schicht_ende`, `ort`, `active`, `review`) VALUES ( ?, '0', ?, ?, ?, ?, '00:00', '-', '1', '0');";
                $data = $conn->sendQuery($sql)->execute([$usr["name"], $usr["email"], $username, $date, $time]);
                if ($data == false) {
                    Exceptions::error_rep("An error occurred while creating easymode worktime entry. See previous message for more information");
                    return false;
                } else {
                    EventDispatcherService::get()->dispatch(new EasymodeWorktimeAddedEvent($username), EasymodeWorktimeAddedEvent::NAME);
                    Exceptions::error_rep("Easymode worktime entry created for user '{$username}'");
                    return true;
                }
            }
        }

        public static function end_easymode_worktime($username, $id)
        {
            $nodes = new Nodes;
            if (!$nodes->checkNode("arbeitszeit.inc", "end_easymode_worktime")) {
                return false;
            }
            Exceptions::error_rep("Ending easymode worktime for user '{$username}'...");
            $time = date("H:i");
            $conn = new DB;
            $user = new Benutzer();

            if (!$user->get_user($username)) {
                Exceptions::error_rep("An error occurred while ending easymode worktime for user '{$username}'. User does not exist.");
                return false;
            } else {
                Exceptions::error_rep("Ending easymode worktime for user '{$username}'...");
                $sql = "UPDATE `arbeitszeiten` SET `schicht_ende` = ?, `active` = '0' WHERE `id` = ?;";
                $data = $conn->sendQuery($sql)->execute([$time, $id]);
                if (!$data) {
                    Exceptions::error_rep("An error occurred while ending easymode worktime. See previous message for more information.");
                    return false;
                } else {
                    EventDispatcherService::get()->dispatch(new EasymodeWorktimeEndedEvent($username, (int) $id), EasymodeWorktimeEndedEvent::NAME);
                    Exceptions::error_rep("Easymode worktime ended for user '{$username}'");
                    return true;
                }
            }
        }

        public function start_easymode_pause_worktime($username, $id)
        {
            if (!$this->nodes()->checkNode("arbeitszeit.inc", "start_easymode_pause_worktime")) {
                return false;
            }
            Exceptions::error_rep("Starting easymode pause for user '{$username}'...");
            $time = date("H:i");
            $user = new Benutzer;

            if (!$user->get_user($username)) {
                Exceptions::error_rep("An error occurred while starting user pause for worktime with ID '{$id}' for user '{$username}'. User does not exist.");
                return false;
            } else {
                Exceptions::error_rep("Starting easymode pause for user '{$username}'...");
                $sql = "UPDATE `arbeitszeiten` SET `pause_start` = ? WHERE id = ?;";
                $data = $this->db->sendQuery($sql)->execute([$time, $id]);
                if (!$data) {
                    Exceptions::error_rep("An error occurred while starting user pause for worktime with ID '{$id}' for user '{$username}'. See previous message for more information.");
                    return false;
                } else {
                    EventDispatcherService::get()->dispatch(new EasymodeWorktimePauseStartEvent($username, (int) $id), EasymodeWorktimePauseStartEvent::NAME);
                    Exceptions::error_rep("Easymode pause started for user '{$username}'");
                    return true;
                }
            }
        }
        public function end_easymode_pause_worktime($username, $id)
        {
            if (!$this->nodes()->checkNode("arbeitszeit.inc", "end_easymode_pause_worktime")) {
                return false;
            }
            Exceptions::error_rep("Ending easymode pause for user '{$username}'...");
            $time = date("H:i");
            $user = new Benutzer;

            if (!$user->get_user($username)) {
                Exceptions::error_rep("An error occurred while ending user pause for worktime with ID '{$id}' for user '{$username}'. User does not exist.");
                return false;
            } else {
                Exceptions::error_rep("Ending easymode pause for user '{$username}'...");
                $sql = "UPDATE `arbeitszeiten` SET `pause_end` = ? WHERE id = ?;";
                $data = $this->db->sendQuery($sql)->execute([$time, $id]);
                if (!$data) {
                    Exceptions::error_rep("An error occurred while ending user pause for worktime with ID '{$id}' for user '{$username}'. See previous message for more information.");
                    return false;
                } else {
                    EventDispatcherService::get()->dispatch(new EasymodeWorktimePauseEndEvent($username, (int) $id), EasymodeWorktimePauseEndEvent::NAME);
                    Exceptions::error_rep("Easymode pause ended for user '{$username}'");
                    return true;
                }
            }
        }

        public function toggle_easymode($username)
        {
            if (!$this->nodes()->checkNode("arbeitszeit.inc", "toggle_easymode")) {
                return false;
            }
            Exceptions::error_rep("Toggling easymode for user '{$username}'...");
            $sql = "SELECT * FROM `users` WHERE username = ?;";
            $res = $this->db->sendQuery($sql);
            $res->execute([$username]);
            if (!$res) {
                Exceptions::error_rep("An error occurred while toggling easymode for user '{$username}'!");
                return false;
            } else {
                $data = $res->fetch(\PDO::FETCH_ASSOC);
                if ($data["easymode"] == false) {
                    $sql1 = "UPDATE `users` SET `easymode` = '1' WHERE username = ?;";
                    $res1 = $this->db->sendQuery($sql1)->execute([$username]);
                    if (!$res1) {
                        Exceptions::error_rep("An error occurred while toggling easymode for user '{$username}'! Could not enable mode.");
                        return false;
                    }
                    return true;
                } else {
                    $sql1 = "UPDATE `users` SET `easymode` = '0' WHERE username = ?;";
                    $res1 = $this->db->sendQuery($sql1)->execute([$username]);
                    if (!$res1) {
                        Exceptions::error_rep("An error occurred while toggling easymode for user '{$username}'! Could not disable mode.");
                        return false;
                    }
                    Exceptions::error_rep("Easymode disabled for user '{$username}'");
                    return true;
                }
            }
        }

        public function get_easymode_status($username, $mode = 0)
        {
            if (!$this->nodes()->checkNode("arbeitszeit.inc", "get_easymode_status")) {
                return false;
            }
            Exceptions::error_rep("Getting easymode status for user '{$username}'...");
            $sql = "SELECT * FROM `users` WHERE username = ?;";
            $res = $this->db->sendQuery($sql);
            $res->execute([$username]);
            if (!$res) {
                Exceptions::error_rep("An error occurred while getting status easymode for user '{$username}'!");
                return false;
            } else {
                $data = $res->fetch(\PDO::FETCH_ASSOC);
                if ($data["easymode"] == true) {
                    if ($mode == 1) {
                        Exceptions::error_rep("Easymode is enabled for user '{$username}'");
                        return true;
                    }
                    return "{$this->i18n["easymode_enabled"]}";
                } else {
                    if ($mode == 1) {
                        Exceptions::error_rep("Easymode is disabled for user '{$username}'");
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
            $nodes = new Nodes;
            if (!$nodes->checkNode("arbeitszeit.inc", "check_easymode_worktime_finished")) {
                return false;
            }
            Exceptions::error_rep("Checking easymode worktime for user '{$username}'...");
            $db = new DB;
            $sql = "SELECT * FROM `arbeitszeiten` WHERE active = '1' AND username = ?;";
            $res = $db->sendQuery($sql);
            $res->execute([$username]);
            if (!$res) {
                Exceptions::error_rep("An error occurred while checking user entries for easymode worktime");
                return false;
            } else {
                if ($res->rowCount() > 1) {
                    Exceptions::error_rep("An error occurred while checking user entries for easymode worktime. Duplicated easymode entry found, please fix manually.");
                    return false;
                } elseif ($res->rowCount() < 1) {
                    Exceptions::error_rep("No easymode worktime found for user '{$username}'");
                    return -1;
                } else {
                    Exceptions::error_rep("Easymode worktime found for user '{$username}'");
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
         * @param string $Wtype Type of worktime
         * @return bool Gibt true bei Erfolg und false bei einem Fehler zurück
         */
        public function add_worktime($start, $end, $location, $date, $username, $type, $Wtype = 0, $pause = null, $meta = null)
        {
            if (!$this->nodes()->checkNode("arbeitszeit.inc", "add_worktime")) {
                return false;
            }
            $user = new Benutzer;
            $usr = $user->get_user($username);
            if ($date > date("y-m-d") /*|| $date < date("y-m-d")*/) {
                Exceptions::error_rep("An error occurred while creating an worktime entry. The date is in the future.");
                return false;
            }
            if (!$user->get_user($username)) {
                Exceptions::error_rep("An error occurred while creating an worktime entry. The user does not exist.");
                return false;
            } else {

                if ($this->type_from_int($Wtype) == false) {
                    Exceptions::error_rep("An error occurred while creating an worktime entry. The worktime type does not exist.");
                    return false;
                }

                Exceptions::error_rep("Creating worktime entry for user '{$username}'...");
                $sql = "INSERT INTO `arbeitszeiten` (`name`, `id`, `email`, `username`, `schicht_tag`, `schicht_anfang`, `schicht_ende`, `ort`, `review`, `active`, `type`, `Wtype`, `pause_start`, `pause_end`, `attachements`) VALUES ( ?, '0', ?, ?, ?, ?, ?, ?, '0', '0', ?, ?, ?, ?, ?);";
                $data = $this->db->sendQuery($sql);
                $data->execute([$usr["name"], $usr["email"], $username, $date, $start, $end, $location, $type, $Wtype, $pause["start"], $pause["end"], $meta]);
                if (!$data) {
                    Exceptions::error_rep("An error occurred while creating an worktime entry. See previous message for more information.");
                    return false;
                } else {
                    EventDispatcherService::get()->dispatch(new WorktimeAddedEvent($username, ["start" => $start, "end" => $end], $Wtype), WorktimeAddedEvent::NAME);
                    Exceptions::error_rep("Worktime entry for user '{$username}' created successfully.");
                    return true;
                }
            }
        }

        public function update_worktime($id, $array)
        {
            if(!$this->check_if_for_review($id)){
                return false;
            }
            $allowed = [
                "schicht_anfang",
                "schicht_ende",
                "ort"
            ];

            $fields = [];
            $values = [];

            foreach ($allowed as $col) {
                if (isset($array[$col]) && $array[$col] !== "" && $array[$col] !== null && $array[$col] !== false) {
                    $fields[] = "$col = ?";
                    $values[] = $array[$col];
                }
            }

            if (empty($fields)) {
                return false;
            }

            $sql = "UPDATE arbeitszeiten SET " . implode(", ", $fields) . " WHERE id = ?";
            $values[] = $id;

            try {
                $stmt = $this->db->sendQuery($sql);
                $ok = $stmt->execute($values);

                if (!$ok) {
                    return false;
                }

                if ($stmt->rowCount() === 0) {
                    return false;
                }

                return true;
            } catch (\PDOException $e) {
                Exceptions::error_rep("[WORKTIME] Update failed: " . $e->getMessage());
                return false;
            }
        }



        public static function type_from_int($int)
        {
            $types = json_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . self::get_app_ini()["config"]["worktime_types"]), true);
            if (isset($types[$int])) {
                return $types[$int];
            } else {
                Exceptions::error_rep("An error occurred while getting worktime type from int. The type does not exist. Returning default");
                return $types[0];
            }

        }

        public static function get_all_types()
        {
            $types = json_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . self::get_app_ini()["config"]["worktime_types"]), true);
            if (isset($types)) {
                return $types;
            } else {
                Exceptions::error_rep("An error occurred while getting worktime types. The types do not exist. Returning default");
                return [];
            }
        }

        /**
         * get_app_ini - Reads the app.json file
         * 
         * @return array Returns the app.json as an array
         */
        public static function get_app_ini()
        {
            $ini_path = dirname(__DIR__, 3) . "/api/v1/inc/app.ini";
            $json_path = dirname(__DIR__, 3) . "/api/v1/inc/app.json";

            Exceptions::error_rep("Loading application configuration...");

            if (file_exists($json_path)) {
                $json_data = file_get_contents($json_path);
                $decoded_data = json_decode($json_data, true);

                if (is_array($decoded_data)) {
                    Exceptions::error_rep("Loaded configuration from app.json");
                    return self::sanitizeOutput($decoded_data);
                } else {
                    Exceptions::error_rep("Invalid JSON format in app.json, falling back to app.ini...");
                }
            }

            if (file_exists($ini_path)) {
                $ini_data = parse_ini_file($ini_path, true);

                if (!is_array($ini_data)) {
                    Exceptions::error_rep("Error parsing app.ini", 1, "N/A");
                    return [];
                }

                Exceptions::error_rep("Migrating app.ini to app.json...");
                file_put_contents($json_path, json_encode($ini_data, JSON_PRETTY_PRINT));

                return self::sanitizeOutput($ini_data);
            }

            Exceptions::error_rep("No valid configuration file found", 1, "N/A");
            return [];
        }

        private static function sanitizeOutput($data)
        {
            if (is_array($data)) {
                return array_map([self::class, 'sanitizeOutput'], $data);
            }
            return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        }

        public function get_all_worktime()
        {
            if (!$this->nodes()->checkNode("arbeitszeit.inc", "get_all_worktime")) {
                return false;
            }
            Exceptions::error_rep("Getting all worktimes...");
            $sql = "SELECT * FROM `arbeitszeiten`;";
            $res = $this->db->sendQuery($sql);
            $res->execute();
            $arr = [];
            if ($res->rowCount() == 0) {
                Exceptions::error_rep("No shifts found");
                return false;
            }
            while ($row = $res->fetch(\PDO::FETCH_ASSOC)) {
                $arr[$row["id"]] = $row;
            }
            return $arr;
        }

        public function get_all_user_worktime($username)
        {
            if (!$this->nodes()->checkNode("arbeitszeit.inc", "get_all_user_worktime")) {
                return false;
            }
            Exceptions::error_rep("Getting all worktimes for user '{$username}'...");
            $sql = "SELECT * FROM `arbeitszeiten` WHERE username = ?;";
            $res = $this->db->sendQuery($sql);
            $res->execute([$username]);
            $arr = [];
            if ($res->rowCount() == 0) {
                Exceptions::error_rep("No shifts found for user '{$username}'");
                return false;
            }
            while ($row = $res->fetch(\PDO::FETCH_ASSOC)) {
                $arr[$row["id"]] = $row;
            }
            return $arr;
        }

        public function get_specific_worktime_html(int $month, int $year)
        {
            if (!$this->nodes()->checkNode("arbeitszeit.inc", "get_specific_worktime_html")) {
                return false;
            }
            Exceptions::error_rep("Getting worktimes rendered for month '{$month}' and year '{$year}'...");
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
                    $rqw = $row["id"];
                    $rbn = $row["username"];
                    $rtn = $this->type_from_int($row["Wtype"]) ?? "N/A";
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

                    $data = <<<DATA

                        <tr>
                            <td><a href="http://{$base_url}/suite/worktime/view_csv.php?mitarbeiter={$rnw2}&{$rrr2}">{$this->i18n["csv"]}</a> {$this->i18n["or"]} <a href="http://{$base_url}/suite/worktime/view_pdf.php?mitarbeiter={$rnw2}&{$rrr2}" target="_blank">{$this->i18n["print"]} $rnw</a>, <a href="http://{$base_url}/suite/admin/actions/worktime/delete.php?id={$rqw}&u={$rbn}">{$this->i18n["delete_entry"]}</a> {$this->i18n["or"]} $rmm</td>
                            <td>$raw</td>
                            <td>$rew</td>
                            <td>$rol</td>
                            <td>$rps</td>
                            <td>$rpe</td>
                            <td>$rum $rno</td>
                            <td>$rtn</td>
                            <td>$rqw</td>
                        </tr>


                        DATA;


                    echo $data;

                }
            } else {
                Exceptions::error_rep("No shifts found for month '{$month}' and year '{$year}'");
                return "{$this->i18n["no_shifts"]}";
            }

        }
        public function get_employee_worktime_html($username)
        {
            if (!$this->nodes()->checkNode("arbeitszeit.inc", "get_employee_worktime_html")) {
                return false;
            }
            Exceptions::error_rep("Getting worktimes rendered for user '{$username}'...");
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
                    $rtn = $this->type_from_int($row["Wtype"]) ?? "N/A";
                    $rps = @strftime("%H:%M", strtotime($row["pause_start"]));
                    $rpe = @strftime("%H:%M", strtotime($row["pause_end"]));

                    if ($rps == "01:00" || $rps == null) {
                        $rps = "-";
                    }
                    if ($rpe == "01:00" || $rpe == null) {
                        $rpe = "-";
                    }

                    if ($row["review"] == 1 || $row["review"] == '1') {
                        $rmn = "style='color: red;'" ?? null;
                        $rno = "&#9888; {$this->i18n["to_review"]} &#9888;" ?? null;
                        $rrr = "| <a href='/suite//worktime/correction.php?id={$rqw}'>{$this->i18n["propose_correction"]}</a>" ?? null;
                    } else {
                        $rmn = null;
                        $rno = null;
                        $rrr = null;
                    }
                    $data = <<<DATA

                        <tr>
                            <td $rmn>$raw</td>
                            <td $rmn>$rew</td>
                            <td $rmn>$rol</td>
                            <td>$rps</td>
                            <td>$rpe</td>
                            <td $rmn>$rum $rno $rrr</td>
                            <td>$rtn</td>
                            <td>$rqw</td>
                        </tr>


                        DATA;



                    echo $data;
                }
            } else {
                Exceptions::error_rep("No shifts found for user '{$username}'");
                return "{$this->i18n["no_shifts"]}";
            }
        }

        public function mark_for_review($id)
        {
            if (!$this->nodes()->checkNode("arbeitszeit.inc", "mark_for_review")) {
                return false;
            }
            Exceptions::error_rep("Marking worktime with ID '{$id}' for review...");
            $sql = "UPDATE `arbeitszeiten` SET `review` = '1' WHERE `id` = ?;";
            $res = $this->db->sendQuery($sql)->execute([$id]);
            if (!$res) {
                Exceptions::error_rep("An error occurred while marking an worktime as under review, id '{$id}'. See previous message for more information.");
                return false;
            } else {
                EventDispatcherService::get()->dispatch(new WorktimeMarkedForReviewEvent($_SESSION["username"], (int) $id), WorktimeMarkedForReviewEvent::NAME);
                return true;
            }
        }

        public function check_if_for_review($id)
        {
            Exceptions::error_rep("Checking if worktime with ID '{$id}' is for review...");
            $sql = "SELECT * FROM `arbeitszeiten` WHERE id = ? AND review = ?";
            $res = $this->db->sendQuery($sql);
            $res->execute([$id, 1]);

            if (!$res) {
                Exceptions::error_rep("An error occured while checking if worktime is for review. See previous message for more information.");
                return false;
            } else {
                if ($res->rowCount() == 1) {
                    return true;
                }
            }
        }

        public function unlock_for_review($id)
        {
            if (!$this->nodes()->checkNode("arbeitszeit.inc", "unlock_for_review")) {
                return false;
            }
            Exceptions::error_rep("Unlocking worktime from review with ID '{$id}'...");
            $sql = "UPDATE `arbeitszeiten` SET `review` = '0' WHERE `id` = ?;";
            $res = $this->db->sendQuery($sql)->execute([$id]);
            if (!$res) {
                Exceptions::error_rep("An error occurred while unlocking an worktime from review, id '{$id}'. See previous message for more information.");
                return false;
            } else {
                EventDispatcherService::get()->dispatch(new WorktimeUnlockedFromReviewEvent($_SESSION["username"], (int) $id), WorktimeUnlockedFromReviewEvent::NAME);
                return true;
            }
        }

        public function calculate_hours_specific_time($username, $month, $year)
        {
            Exceptions::error_rep("Calculating hours for user '{$username}' in month '{$month}' and year '{$year}'...");
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

        public static function fix_easymode_worktime($username)
        {
            $db = new DB(); // Erstellen der DB-Instanz
            Exceptions::error_rep("[ARBEITSZEIT] Trying to fix easymode worktime for user '{$username}'...");
            // Alle aktiven Arbeitszeiten des Nutzers abrufen, sortiert nach ID (neueste zuerst)
            $query = "SELECT id FROM worktimes WHERE username = :username AND active = 1 ORDER BY id DESC";
            $statement = $db->sendQuery($query);
            $statement->bindValue(':username', $username);
            $statement->execute();
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);

            if (count($result) > 1) {
                Exceptions::error_rep("[ARBEITSZEIT] Found multiple active worktimes for user '{$username}'");
                $latestId = $result[0]['id'];

                $idsToDisable = array_column(array_slice($result, 1), 'id');

                $placeholder = implode(',', array_fill(0, count($idsToDisable), '?'));
                $disableQuery = "UPDATE worktimes SET active = 0 WHERE id IN ($placeholder)";
                $disableStatement = $db->sendQuery($disableQuery);
                foreach ($idsToDisable as $index => $id) {
                    Exceptions::error_rep("[ARBEITSZEIT] Disabling worktime with ID '{$id}'");
                    $disableStatement->bindValue($index + 1, $id);
                }
                $disableStatement->execute();

                $activateQuery = "UPDATE worktimes SET active = 1 WHERE id = :latestId";
                $activateStatement = $db->sendQuery($activateQuery);
                $activateStatement->bindValue(':latestId', $latestId);
                $activateStatement->execute();
                EventDispatcherService::get()->dispatch(new FixEasymodeWorktimeEvent($username), FixEasymodeWorktimeEvent::NAME);
                Exceptions::error_rep("[ARBEITSZEIT] Finished fixing attempt for user '{$username}'");
            }
        }


        public function blockIfNotAdmin()
        {
            if (!Benutzer::is_admin(Benutzer::get_user($_SESSION["username"]))) {
                $this->statusMessages()->redirect("error");
            }
            return false;
        }

        public function global_dispatcher(): \Symfony\Component\EventDispatcher\EventDispatcher
        {
            return \Arbeitszeit\Events\EventDispatcherService::get();
        }

        public function getTimeTrackVersion()
        {
            return file_get_contents(dirname(__DIR__, 3) . "/VERSION");
        }

        public function getToilVersion()
        {
            return file_get_contents(dirname(__DIR__, 1) . "/toil/VERSION");
        }

        public function notifications(): Notifications
        {
            if (!$this->notifications)
                $this->notifications = new Notifications;
            return $this->notifications;
        }

        public function db(): DB
        {
            if (!$this->db)
                $this->db = new DB;
            return $this->db;
        }

        public function i18n(): i18n
        {
            if (!$this->i18nC)
                $this->i18nC = new i18n;
            return $this->i18nC;
        }

        public function benutzer(): Benutzer
        {
            if (!$this->benutzer)
                $this->benutzer = new Benutzer;
            return $this->benutzer;
        }

        public function auth(): Auth
        {
            if (!$this->auth)
                $this->auth = new Auth;
            return $this->auth;
        }

        public function mode(): Mode
        {
            if (!$this->mode)
                $this->mode = new Mode;
            return $this->mode;
        }

        public function autodelete(): Autodelete
        {
            if (!$this->autodelete)
                $this->autodelete = new Autodelete;
            return $this->autodelete;
        }

        public function sickness(): Sickness
        {
            if (!$this->sickness)
                $this->sickness = new Sickness;
            return $this->sickness;
        }

        public function vacation(): Vacation
        {
            if (!$this->vacation)
                $this->vacation = new Vacation;
            return $this->vacation;
        }

        public function exportModule(): ExportModule
        {
            if (!$this->exportModule)
                $this->exportModule = new ExportModule;
            return $this->exportModule;
        }

        public function mails(): Mails
        {
            if (!$this->mails)
                $this->mails = new Mails;
            return $this->mails;
        }

        public function nodes(): Nodes
        {
            if (!$this->nodes)
                $this->nodes = new Nodes;
            return $this->nodes;
        }

        public function statusMessages(): StatusMessages
        {
            if (!$this->statusMessages)
                $this->statusMessages = new StatusMessages;
            return $this->statusMessages;
        }

        public function projects(): Projects
        {
            if (!$this->projects)
                $this->projects = new Projects;
            return $this->projects;
        }
    }
}



?>