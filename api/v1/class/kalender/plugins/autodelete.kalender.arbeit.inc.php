<?php
namespace Arbeitszeit{
    /**
     * Autodelete - Erweitert die Kalender-Klasse um das automatische Löschen von Kalendereinträgen
     */
    class Autodelete extends Kalender {

        /**
         * autodelete_obsolete_calendar_entries() - Löscht abgelaufene Kalendereinträge
         * 
         * @return void|array Gibt nichts bei Erfolg zurück - Bei einem Fehler ein Fehler-Array
         * @author Bryan Böhnke-Avan <bryan.boehnke04@gmail.com>
         */
        public function autodelete_obsolete_calendar_entries(){
            $conn = parent::get_conn();
            $sql = "DELETE FROM `kalender` WHERE `datum` < NOW();";
            mysqli_query($conn, $sql);
            if(mysqli_error($conn)){
                Exceptions::error_rep("An error occured while autodeleting calendar entries. | SQL-Error: " . mysqli_error($conn));
                return [
                    "error" => [
                        "error_code" => 7,
                        "error_message" => "Error while auto deleting obsolete calendar entries!"
                    ]
                ];
            } 
        }
    }
}




?>