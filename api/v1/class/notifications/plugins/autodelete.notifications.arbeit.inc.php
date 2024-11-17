<?php
namespace Arbeitszeit{
    /**
     * Autodelete - Extends the Notifications class to provide autodelete functionality
     */
    class Autodelete extends Notifications {

        /**
         * autodelete_obsolete_notifications_entries() - Deletes obsolete notifications entries
         * 
         * @return void|array Returns nothing on success, an array with an error message on failure
         * @author Bryan Böhnke-Avan <github@openducks.org>
         */
        public function autodelete_obsolete_notifications_entries(){
            $db = new DB;
            $sql = "DELETE FROM `kalender` WHERE `datum` < NOW();";
            $data = $db->sendQuery($sql)->execute();
            if($data == false){
                Exceptions::error_rep("An error occured while autodeleting notifications entries. See previous message for more information.");
                return [
                    "error" => [
                        "error_code" => 7,
                        "error_message" => "Error while auto deleting obsolete notifications entries!"
                    ]
                ];
            } 
        }
    }
}




?>