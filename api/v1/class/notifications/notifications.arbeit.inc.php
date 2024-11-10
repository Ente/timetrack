<?php
namespace Arbeitszeit{
    use Arbeitszeit\Arbeitszeit;
    class Notifications extends Arbeitszeit{


        public array $i18n;

        public $db;

        public function __construct(){
            $i18n = new i18n;
            $this->db = new DB;
            $this->i18n = $i18n->loadLanguage(null, "class/notifications");
        }


        /**
         * calender_delete() - Deletes all calendar entries that are older than the current date
         * 
         * @return boolean|array Returns "true" on success and an error array on failure
         * 
         */
        public function calender_delete(){
            $sql = "DELETE FROM `kalender` WHERE `datum` < NOW();";
            $data = $this->db->sendQuery($sql)->execute();
            if($data == false){
                Exceptions::error_rep("An error occured while deleting expired calendar entries. See previous message for more information.");
                return [
                    "error" => [
                        "error_code" => 1,
                        "error_message" => "Error while executing a query!"
                    ]
                ];
            } else {
                return 1;
            }
        }

        public function get_calendar(){
            $sql = "SELECT * FROM `kalender`;";
            $res = $this->db->sendQuery($sql);
            $res->execute();
            $count = $res->rowCount();
            if($count >=1){
                $data = $res->fetch(\PDO::FETCH_ASSOC);
                $date = @strftime("%d.%m.%Y", strtotime($data["datum"]));
                $data["datum_new"] = $date;

                return $data;
            } else {
                return "{$this->i18n["no_data"]}";
            }
        }

        public function get_calendar_edit_html(){
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
                        <td><a href="../admin/calendar/delete.php?id={$id}">{$this->i18n["delete"]}</a> | <a href="../admin/calendar/edit.php?id={$id}">{$this->i18n["edit"]}</a></td>
                        <td>$date</td>
                        <td>$time</td>
                        <td>$location</td>
                        <td>$note</td>
                    </tr>
                    DATA;
                    return $data;
                }
            } else {
                return "<p>{$this->i18n["no_entries"]}</p>";
            }
        }

        public function get_calendar_html(){
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
                    <a href="http://{$base_url}/suite/calendar/view.php?id={$id}"><div>
                        <h2>{$this->i18n["calendar_notify"]} $date</h2>
                        <p><b>$location</b>: $note | <span><b>$date</b></span> - $time</p>
                    </div></a>
                    DATA;
                }
                
                echo $html;
              
            } else {
                return null;
            }
        }

        /**
         * get_calendar_entry() - Loads a calendar entry by its ID
         * 
         * @param int $id The ID of the calendar entry
         * @return array Returns the calendar entry as an array or an error array
         * 
         */
        public function get_calendar_entry($id){
            $sql = "SELECT * FROM `kalender` WHERE id = ?";
            $res = $this->db->sendQuery($sql);
            $res->execute([$id]);
            $count = $res->rowCount();

            if($count == 1){
                $data = $res->fetch(\PDO::FETCH_ASSOC);
                $datum = @strftime("%d.%m.%Y", strtotime($data["datum"]));

                $data["datum_new"] = $datum;

                return $data;
            } else {
                Exceptions::error_rep("An error occured while getting an calendar entry. Entry could not be found for id '$id'.");
                return [
                    "error" => [
                        "error_code" => 5,
                        "error_message" => "Error while fetching calendar entry!"
                    ]
                ];
            }
        }

        /**
         * create_calendar_entry() - Creates a new calendar entry
         * 
         * @param time $time The time (usually HH:MM)
         * @param date $date The date (usually YYYY-MM-DD)
         * @param string $location The location
         * @param string $comment A comment for the calendar entry
         * @param bool|array Returns "true" on success and an error array on failure
         */
        public function create_calendar_entry($time, $date, $location, $comment){
            $sql = "INSERT INTO `kalender` (`datum`, `uhrzeit`, `ort`, `notiz`) VALUES (?, ?, ?, ?)";
            $res = $this->db->sendQuery($sql)->execute([$date, $time, $location, $comment]);
            if(!$res){
                Exceptions::error_rep("An error occured while creating an calendar entry. See previous message for more information.");
                return [
                    "error" => [
                        "error_code" => 6,
                        "error_message" => "Error while creating calendar entry!"
                    ]
                ];
            } else {
                return true;
            }
        }

        /**
         * edit_calendar_entry() - Edits a calendar entry
         * 
         * @param time $time The time (usually HH:MM)
         * @param date $date The date (usually YYYY-MM-DD)
         * @param string $location The location
         * @param string $comment A comment for the calendar entry
         * @return bool|array Returns "true" on success and an error array on failure
         * @Note All parameters are required, if not set, the function will return an error array 
         */
        public function edit_calendar_entry($id, $time, $date, $location, $comment){
            $sql = "UPDATE `kalender` SET `datum` = ?, `uhrzeit` = ?, `ort` = ?, `notiz` = ? WHERE id = ?;";
            $res = $this->db->sendQuery($sql)->execute([$date, $time, $location, $comment, $id]);
            if(!$res){
                Exceptions::error_rep("An error occured while editing an calendar entry. See previous message for more information.");
                return [
                    "error" => [
                        "error_code" => 8,
                        "error_message" => "Error while editing calendar entry!"
                    ]
                ];
            } else {
                return true;
            }
        }

        /**
         * delete_calendar_entry() - Deletes a calendar entry
         * 
         * @param int $id The ID of the calendar entry
         * @return bool|array Returns "true" on success and an error array on failure
         */
        public function delete_calendar_entry($id){
            $sql = "DELETE FROM `kalender` WHERE id = ?;";
            $res = $this->db->sendQuery($sql)->execute([$id]);
            if(!$res){
                Exceptions::error_rep("An error occured while deleting an calendar entry. See previous message for more information.");
                return [
                    "error" => [
                        "error_code" => 9,
                        "error_message" => "Error while deleting calendar entry!"
                    ]
                ];
            } else {
                return true;
            }
        }

    }
}



?>