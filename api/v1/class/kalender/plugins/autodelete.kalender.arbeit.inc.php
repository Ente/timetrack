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
            $db = new DB;
            $sql = "DELETE FROM `kalender` WHERE `datum` < NOW();";
            $data = $db->sendQuery($sql)->execute();
            if($data == false){
                Exceptions::error_rep("An error occured while autodeleting calendar entries. See previous message for more information.");
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