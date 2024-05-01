<?php
namespace Arbeitszeit{
    /**
     * Diese Klasse beinhaltet alle Elemente, die für den Kalender relevant sind
     * Erweitert die "Arbeitszeit" Klasse
     * 
     * @author Bryan Böhnke-Avan <bryan.boehnke04@gmail.com>
     */
    use Arbeitszeit\Arbeitszeit;
    class Kalender extends Arbeitszeit{



        public function __construct(){
            
        }


        /**
         * calender_delete() - Löscht abgelaufene Einträge aus den Kalender
         * 
         * @return boolean|array Gibt "true" zurück, bei Fehlern ein Array
         * 
         */
        public function calender_delete(){
            $conn = $this->get_conn();
            $sql = "DELETE FROM `kalender` WHERE `datum` < NOW();";
            mysqli_query($conn, $sql);
            if(mysqli_error($conn)){
                $mysql_error = mysqli_error($conn);
                Exceptions::error_rep("An error occured while deleting expired calendar entries. SQL-Error: " . mysqli_error($conn));
                return [
                    "error" => [
                        "error_code" => 1,
                        "error_message" => "Error while executing a query: {$mysql_error}"
                    ]
                ];
            } else {
                return 1;
            }
        }

        public function get_calendar(){
            $conn = Arbeitszeit::get_conn();
            $sql = "SELECT * FROM `kalender`;";
            $res = mysqli_query($conn, $sql);
            $count = mysqli_num_rows($res);
            if($count >=1){
                $data = mysqli_fetch_assoc($res);
                $date = strftime("%d.%m.%Y", strtotime($data["datum"]));
                $data["datum_new"] = $date;

                return $data;
            } else {
                return "No data";
            }
        }

        public function get_calendar_edit_html(){
            $conn = Arbeitszeit::get_conn();
            $sql = "SELECT * FROM `kalender` ORDER BY id DESC;";
            $res = mysqli_query($conn, $sql);
            if(mysqli_num_rows($res) > 0){
                while($row = mysqli_fetch_assoc($res)){
                    $time = $row["uhrzeit"];
                    $date = strftime("%d.%m.%Y", strtotime($row["datum"]));
                    $location = $row["ort"];
                    $note = $row["notiz"];
                    $id = $row["id"];

                    $data = <<< DATA
                    <tr>
                        <td><a href="../admin/calendar/delete.php?id={$id}">Eintrag löschen</a> | <a href="../admin/calendar/edit.php?id={$id}">Eintrag bearbeiten</a></td>
                        <td>$date</td>
                        <td>$time</td>
                        <td>$location</td>
                        <td>$note</td>
                    </tr>
                    DATA;
                    return $data;
                }
            } else {
                return "<p>Keine Einträge gefunden!</p>";
            }
        }

        public function get_calendar_html(){
            $conn = Arbeitszeit::get_conn();
            $base_url = Arbeitszeit::get_app_ini()["general"]["base_url"];
            $sql = "SELECT * FROM `kalender` ORDER BY id DESC;";
            $res = mysqli_query($conn, $sql);
            $html = null;
            if(mysqli_num_rows($res) > 0){
                while($row = mysqli_fetch_assoc($res)){
                    $location = $row["ort"];
                    $date = strftime("%d.%m.%Y", strtotime($row["datum"]));
                    $time = $row["uhrzeit"];
                    $note = $row["notiz"];
                    $id = $row["id"];
                    
                    $html = <<< DATA
                    <a href="http://{$base_url}/suite/calendar/view.php?id={$id}"><div>
                        <h2>Kalender Mitteilung für den $date</h2>
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
         * get_calendar_entry() - Lädt einen spezifischen Eintrag aus der Datenbank
         * 
         * @param int $id ID des Kalendereintrags
         * @return array Gibt ein Array mit den Daten des Kalendereintrags zurück - Bei einem Fehler ein Fehler-Array
         * 
         */
        public function get_calendar_entry($id){
            $conn = $this->get_conn();
            $sql = "SELECT * FROM `kalender` WHERE id = '{$id}';";
            $res = mysqli_query($conn, $sql);
            $count = mysqli_num_rows($res);

            if($count == 1){
                $data = mysqli_fetch_assoc($res);
                $datum = strftime("%d.%m.%Y", strtotime($data["datum"]));

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
         * create_calendar_entry() - Erstellt einen Kalendereintrag
         * 
         * @param time $time Die vom Nutzer eingegebene Zeit im Format des Browsers (normalerweise HH:MM)
         * @param date $date Das vom Nutzer eingetragene Datum im Format des Browsers (normalerweise YYYY-MM-DD)
         * @param string $location Der vom Nutzer eingetragene Ort
         * @param string $comment Ein Kommentar für den Kalendereintrag
         * @param bool|array Gibt "true" bei Erfolg zurück und ein Fehler-Array bei einem Fehler
         */
        public function create_calendar_entry($time, $date, $location, $comment){
            $conn = $this->get_conn();
            $sql = "INSERT INTO `kalender` (`datum`, `uhrzeit`, `ort`, `notiz`) VALUES ('{$date}', '{$time}', '{$location}', '{$comment}')";
            mysqli_query($conn, $sql);
            if(mysqli_error($conn)){
                Exceptions::error_rep("An error occured while creating an calendar entry. | SQL-Error: " . mysqli_error($conn));
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
         * edit_calendar_entry() - Bearbeitet einen Kalendereintrag (IRREVERSIBEL)
         * 
         * @param time $time Die vom Nutzer eingegebene Zeit im Format des Browsers (normalerweise HH:MM)
         * @param date $date Das vom Nutzer eingetragene Datum im Format des Browsers (normalerweise YYYY-MM-DD)
         * @param string $location Der vom Nutzer eingetragene Ort
         * @param string $comment Ein Kommentar für den Kalendereintrag 
         * @return bool|array Gibt "true" bei Erfolg zurück und ein Fehler-Array bei einem Fehler
         * @Hinweis Es müssen alle Felder ausgefüllt sein, da ansonsten leere Felder den Wert NULL bekommen
         */
        public function edit_calendar_entry($id, $time, $date, $location, $comment){
            $conn = $this->get_conn();
            $sql = "UPDATE `kalender` SET `datum` = '{$date}', `uhrzeit` = '{$time}', `ort` = '{$location}', `notiz` = '{$comment}' WHERE id = '{$id}';";
            mysqli_query($conn, $sql);
            if(mysqli_error($conn)){
                Exceptions::error_rep("An error occured while editing an calendar entry. | SQL-Error: " . mysqli_error($conn));
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
         * delete_calendar_entry() - Löscht einen Kalendereintrag
         * 
         * @param int $id Die ID des zu löschenden Kalendereintrags
         * @return bool|array Gibt "true" bei Erfolg zurück und ein Fehler-Array bei einem Fehler
         */
        public function delete_calendar_entry($id){
            $conn = $this->get_conn();
            $sql = "DELETE FROM `kalender` WHERE id = '{$id}';";
            mysqli_query($conn, $sql);
            if(mysqli_error($conn)){
                Exceptions::error_rep("An error occured while deleting an calendar entry. | SQL-Error: " . mysqli_error($conn));
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