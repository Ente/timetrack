<?php
namespace Arbeitszeit{
    use Arbeitszeit\Arbeitszeit;
    use Arbeitszeit\Events\EventDispatcherService;
    use Arbeitszeit\Events\CreatedNotificationEvent;
    use Arbeitszeit\Events\DeletedNotificationEvent;
    use Arbeitszeit\Events\EditedNotificationEvent;
    use Arbeitszeit\Events\DeletedObsoleteNotificationsEvent;
    use LdapTools\Event\Event;

    class Notifications extends Arbeitszeit{


        public array $i18n;

        public $db;

        public function __construct(){
            $i18n = new i18n;
            $this->db = new DB;
            $this->i18n = $i18n->loadLanguage(null, "class/notifications");
        }


        /**
         * calender_delete() - Deletes all notifications entries that are older than the current date
         * 
         * @return boolean|array Returns "true" on success and an error array on failure
         * 
         */
        public function notifications_delete(){
            Exceptions::error_rep("[NOTIFICATIONS] Deleting expired notifications entries...");
            $sql = "DELETE FROM `kalender` WHERE `datum` < NOW();";
            $data = $this->db->sendQuery($sql)->execute();
            if($data == false){
                Exceptions::error_rep("An error occurred while deleting expired notifications entries. See previous message for more information.");
                return [
                    "error" => [
                        "error_code" => 1,
                        "error_message" => "Error while executing a query!"
                    ]
                ];
            } else {
                EventDispatcherService::get()->dispatch(new DeletedObsoleteNotificationsEvent(), DeletedObsoleteNotificationsEvent::NAME);
                Exceptions::error_rep("[NOTIFICATIONS] Deleted expired notifications entries.");
                return 1;
            }
        }

        public function get_notifications_edit_html(){
            if(!$this->nodes()->checkNode("notifications.inc", "get_notifications_edit_html")) return "<p>{$this->i18n["no_entries"]}</p>";
            Exceptions::error_rep("[NOTIFICATIONS] Getting notifications edit html...");
            $sql = "SELECT * FROM `kalender` ORDER BY id DESC;";
            $res = $this->db->sendQuery($sql);
            $res->execute();
            if($res->rowCount() > 0){
                while($row = $res->fetch(\PDO::FETCH_ASSOC)){
                    $time = $row["uhrzeit"];
                    $date = @strftime("%d.%m.%Y", strtotime($row["datum"]));
                    $location = $row["ort"];
                    $note = $row["notiz"];
                    $id = $row["id"];

                    $data = <<< DATA
                    <tr>
                        <td><a href="../admin/notifications/delete.php?id={$id}">{$this->i18n["delete"]}</a> | <a href="../admin/notifications/edit.php?id={$id}">{$this->i18n["edit"]}</a></td>
                        <td>$date</td>
                        <td>$time</td>
                        <td>$location</td>
                        <td>$note</td>
                    </tr>
                    DATA;
                    return $data;
                }
            } else {
                Exceptions::error_rep("[NOTIFICATIONS] No notifications entries found.");
                return "<p>{$this->i18n["no_entries"]}</p>";
            }
        }

        public function get_all_notifications(){
            if(!$this->nodes()->checkNode("notifications.inc", "get_all_notifications")) return null;
            Exceptions::error_rep("[NOTIFICATIONS] Getting all notifications...");
            $sql = "SELECT * FROM `kalender` ORDER BY id DESC;";
            $res = $this->db->sendQuery($sql);
            $res->execute();
            $data = [];
            if($res->rowCount() > 0){
                while($row = $res->fetch(\PDO::FETCH_ASSOC)){
                    $time = $row["uhrzeit"];
                    $date = @strftime("%d.%m.%Y", strtotime($row["datum"]));
                    $location = $row["ort"];
                    $note = $row["notiz"];
                    $id = $row["id"];

                    $data[] = [
                        "id" => $id,
                        "date" => $date,
                        "time" => $time,
                        "location" => $location,
                        "note" => $note
                    ];
                }
                return $data;
            } else {
                Exceptions::error_rep("[NOTIFICATIONS] No notifications entries found.");
                return null;
            }
        }

        public function get_notifications_html(){
            if(!$this->nodes()->checkNode("notifications.inc", "get_notifications_html")) return null;
            Exceptions::error_rep("[NOTIFICATIONS] Getting notifications html...");
            $base_url = Arbeitszeit::get_app_ini()["general"]["base_url"];
            $sql = "SELECT * FROM `kalender` ORDER BY id DESC;";
            $res = $this->db->sendQuery($sql);
            $res->execute();
            $html = null;
            if($res->rowCount() > 0){
                while($row = $res->fetch(\PDO::FETCH_ASSOC)){
                    $location = $row["ort"];
                    $date = @strftime("%d.%m.%Y", strtotime($row["datum"]));
                    $time = $row["uhrzeit"];
                    $note = $row["notiz"];
                    $id = $row["id"];
                    
                    $html = <<< DATA
                    <a href="http://{$base_url}/suite/notifications/view.php?id={$id}" target="_blank">
                    <div class="notification-banner">
                    <h2>{$this->i18n["notifications_notify"]} $date</h2>
                    <p><b>$location</b>: $note | <span><b>$date</b></span> – $time</p>
                    </div>
                    </a>

                    DATA;
                }
                
                echo $html;
              
            } else {
                Exceptions::error_rep("[NOTIFICATIONS] No notifications entries found.");
                return null;
            }
        }

        /**
         * get_notifications_entry() - Loads a notifications entry by its ID
         * 
         * @param int $id The ID of the notifications entry
         * @return array Returns the notifications entry as an array or an error array
         * 
         */
        public function get_notifications_entry($id){
            if(!$this->nodes()->checkNode("notifications.inc", "get_notifications_entry")) return null;
            Exceptions::error_rep("[NOTIFICATIONS] Getting notifications entry with ID '$id'...");
            $sql = "SELECT * FROM `kalender` WHERE id = ?";
            $res = $this->db->sendQuery($sql);
            $res->execute([$id]);
            $count = $res->rowCount();

            if($count == 1){
                $data = $res->fetch(\PDO::FETCH_ASSOC);
                $datum = @strftime("%d.%m.%Y", strtotime($data["datum"]));

                $data["datum_new"] = $datum;
                Exceptions::error_rep("[NOTIFICATIONS] Found notifications entry with ID '$id'.");
                return $data;
            } else {
                Exceptions::error_rep("An error occurred while getting an notifications entry. Entry could not be found for id '$id'.");
                return [
                    "error" => [
                        "error_code" => 5,
                        "error_message" => "Error while fetching notifications entry!"
                    ]
                ];
            }
        }

        /**
         * create_notifications_entry() - Creates a new notifications entry
         * 
         * @param time $time The time (usually HH:MM)
         * @param date $date The date (usually YYYY-MM-DD)
         * @param string $location The location
         * @param string $comment A comment for the notifications entry
         * @param bool|array Returns "true" on success and an error array on failure
         */
        public function create_notifications_entry($time, $date, $location, $comment){
            if(!$this->nodes()->checkNode("notifications.inc", "create_notifications_entry")) return false;
            Exceptions::error_rep("[NOTIFICATIONS] Creating new notifications entry...");
            $sql = "INSERT INTO `kalender` (`datum`, `uhrzeit`, `ort`, `notiz`) VALUES (?, ?, ?, ?)";
            $res = $this->db->sendQuery($sql)->execute([$date, $time, $location, $comment]);
            if(!$res){
                Exceptions::error_rep("An error occurred while creating an notifications entry. See previous message for more information.");
                return [
                    "error" => [
                        "error_code" => 6,
                        "error_message" => "Error while creating notifications entry!"
                    ]
                ];
            } else {
                EventDispatcherService::get()->dispatch(new CreatedNotificationEvent($_SESSION["username"], "N/A", $location, 0), CreatedNotificationEvent::NAME);
                Exceptions::error_rep("[NOTIFICATIONS] Created new notifications entry.");
                return true;
            }
        }

        /**
         * edit_notifications_entry() - Edits a notifications entry
         * 
         * @param time $time The time (usually HH:MM)
         * @param date $date The date (usually YYYY-MM-DD)
         * @param string $location The location
         * @param string $comment A comment for the notifications entry
         * @return bool|array Returns "true" on success and an error array on failure
         * @Note All parameters are required, if not set, the function will return an error array 
         */
        public function edit_notifications_entry($id, $time, $date, $location, $comment){
            if(!$this->nodes()->checkNode("notifications.inc", "edit_notifications_entry")) return false;
            Exceptions::error_rep("[NOTIFICATIONS] Editing notifications entry with ID '$id'...");
            $sql = "UPDATE `kalender` SET `datum` = ?, `uhrzeit` = ?, `ort` = ?, `notiz` = ? WHERE id = ?;";
            $res = $this->db->sendQuery($sql)->execute([$date, $time, $location, $comment, $id]);
            if(!$res){
                Exceptions::error_rep("An error occurred while editing an notifications entry. See previous message for more information.");
                return [
                    "error" => [
                        "error_code" => 8,
                        "error_message" => "Error while editing notifications entry!"
                    ]
                ];
            } else {
                EventDispatcherService::get()->dispatch(new EditedNotificationEvent($_SESSION["username"], (int)$id == 0), EditedNotificationEvent::NAME);
                Exceptions::error_rep("[NOTIFICATIONS] Edited notifications entry with ID '$id'.");
                return true;
            }
        }

        /**
         * delete_notifications_entry() - Deletes a notifications entry
         * 
         * @param int $id The ID of the notifications entry
         * @return bool|array Returns "true" on success and an error array on failure
         */
        public function delete_notifications_entry($id){
            if(!$this->nodes()->checkNode("notifications.inc", "delete_notifications_entry")) return false;
            Exceptions::error_rep("[NOTIFICATIONS] Deleting notifications entry with ID '$id'...");
            $sql = "DELETE FROM `kalender` WHERE id = ?;";
            $res = $this->db->sendQuery($sql)->execute([$id]);
            if(!$res){
                Exceptions::error_rep("An error occurred while deleting an notifications entry. See previous message for more information.");
                return [
                    "error" => [
                        "error_code" => 9,
                        "error_message" => "Error while deleting notifications entry!"
                    ]
                ];
            } else {
                EventDispatcherService::get()->dispatch(new DeletedNotificationEvent($_SESSION["username"], (int)$id ?? 0), DeletedNotificationEvent::NAME);
                Exceptions::error_rep("[NOTIFICATIONS] Deleted notifications entry with ID '$id'.");
                return true;
            }
        }

    }
}



?>