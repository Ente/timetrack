<?php
namespace Arbeitszeit {
    /**
     * vacation
     * 
     * v1
     * - Added function to add vacation
     */
    use Arbeitszeit\Events\EventDispatcherService;
    use Arbeitszeit\Events\VacationCreatedEvent;
    use Arbeitszeit\Events\VacationUpdatedEvent;
    use Arbeitszeit\Events\VacationDeletedEvent;
    class Vacation extends Arbeitszeit
    {

        public $db;

        public function __construct(){
            $this->db = new DB;
        }

        public function add_vacation($start, $stop, $username = null)
        {
            if($this->nodes()->checkNode("vacation.inc", "add_vacation") == false){
                return false;
            }
            Exceptions::error_rep("[VACATION] Adding vacation for user '{$username}'...");
            if($username != null){
                $user = $_SESSION["username"];
            }
            $dateString = $start;
            $format = 'Y-m-d';

            $dateObj = \DateTime::createFromFormat($format, $dateString);

            if ($dateObj == false) {
              Exceptions::error_rep("[VACATION] An error occurred while adding an vacation for user '$user'. Could not validate dateFormat! | String: '{$dateString}', expected: d-m-Y");
              return false;
            }
            $sql = "INSERT INTO `vacation` (`id`, `username`, `start`, `stop`, `status`) VALUES ('0', ?, ?, ?, 'pending') ";
            $data = $this->db->sendQuery($sql)->execute([$user, $start, $stop]);
            if(!$data){
                Exceptions::error_rep("[VACATION] An error occurred while adding an vacation for user '$user'. See previous message for more information.");
                return false;
            } else {
                EventDispatcherService::get()->dispatch(new VacationCreatedEvent($user, $start, $stop), VacationCreatedEvent::NAME);
                Exceptions::error_rep("[VACATION] Successfully added vacation for user '$user'.");
                return true;
            }
        }

        public function remove_vacation($id){ # admin function only
            if($this->nodes()->checkNode("vacation.inc", "remove_vacation") == false){
                return false;
            }
            Exceptions::error_rep("[VACATION] Removing vacation with id '{$id}'...");
            $sql = "DELETE * FROM `vacation` WHERE id = ?";
            $data = $this->db->sendQuery($sql)->execute(array([$id]));
            if($data == false){
                Exceptions::error_rep("[VACATION] An error occurred while deleting a vacation with id '{$id}'. See previous message for more information");
                return false;
            }
            EventDispatcherService::get()->dispatch(new VacationDeletedEvent($_SESSION["username"], (int)$id), VacationDeletedEvent::NAME);
            Exceptions::error_rep("[VACATION] Successfully removed vacation with id '{$id}'.");
            return true;
        }

        public function change_status($id, $new_state = 3) # admin function only
        {
            if($this->nodes()->checkNode("vacation.inc", "change_status") == false){
                return false;
            }
            Exceptions::error_rep("[VACATION] Changing status for vacation id '{$id}' to '{$new_state}'...");
            if($new_state == 1 /* approve */){
                $sql = "UPDATE `vacation` SET `status` = 'approved' WHERE `id` = ?;";
            } elseif($new_state == 2 /* rejected */) {
                $sql = "UPDATE `vacation` SET `status` = 'rejected' WHERE `id` = ?;";
            } else {
                $sql = "UPDATE `vacation` SET `status` = 'pending' WHERE `id` = ?;";
            }
            $data = $this->db->sendQuery($sql)->execute([$id]);
            if($data == false){
                Exceptions::error_rep("[VACATION] An error occurred while setting status for vacaction. id '{$id}', new state: '{$new_state}'. See previous message for more information.");
                return false;
            } else {
                EventDispatcherService::get()->dispatch(new VacationUpdatedEvent($_SESSION["username"], (int)$id, $new_state), VacationUpdatedEvent::NAME);
                Exceptions::error_rep("[VACATION] Successfully changed status for vacation id '{$id}', new state: '{$new_state}'.");
                return true;
            }

        }

        public function get_vacation($id, $mode = 1){
            Exceptions::error_rep("[VACATION] Getting vacation id '{$id}'...");
            $data = $this->db->sendQuery("SELECT * FROM `vacation` WHERE id = ?")->execute([$id]);
            if($data == false){
                Exceptions::error_rep("[VACATION] An error occurred while getting vacaction. id '{$id}'. See previous message for more information.");
                return false;
            } else {
                Exceptions::error_rep("[VACATION] Successfully found vacation id '{$id}' inside database.");
                if($mode == 2){
                    Exceptions::error_rep("[VACATION] Returning 'true' for vacation id '{$id}'...");
                    return true;
                } else {
                    Exceptions::error_rep("[VACATION] Returning data for vacation id '{$id}'...");
                    return $data;
                }
            }
        }

        public function display_vacation_all(){ # admin function only
            if($this->nodes()->checkNode("vacation.inc", "display_vacation_all") == false){
                return false;
            }
            Exceptions::error_rep("[VACATION] Displaying all vacations...");
            $i18n = $this->i18n()->loadLanguage(null, "worktime/vacation/all", "admin");
            
            $sql = "SELECT * FROM `vacation`";
            $data = $this->db->sendQuery($sql);
            $data->execute();
            $count = $data->rowCount();
            if($count > 1){
                # compute and return data
                foreach($data->fetchAll(\PDO::FETCH_ASSOC) as $row){
                    $rnw = $row["username"];
                    $start = @strftime("%d.%m.%Y", strtotime($row["start"]));
                    $stop = @strftime("%d.%m.%Y", strtotime($row["stop"]));
                    $status = $row["status"];
                    $id = $row["id"];

                    switch($status){
                        case "pending":
                            $status = "<span style='color:yellow;'>{$i18n["status"]["pending"]}</span>";
                            break;
                        case "approved":
                            $status = "<span style='color:green;'>{$i18n["status"]["approved"]}</span>";
                            break;
                        case "rejected":
                            $status = "<span style='color:red;'>{$i18n["status"]["rejected"]}</span>";
                            break;
                    }

                    if($stop == "01.01.1970"){
                        $stop = "-";
                    }

                    $data = <<< DATA

                        <tr>
                            <td>{$rnw}</td>
                            <td>{$start}</td>
                            <td>{$stop}</td>
                            <td>{$status} | {$i18n["status"]["set_to"]} <a href="/suite/admin/actions/worktime/state_vacation.php?id={$id}&new=pending&u={$rnw}">{$i18n["status"]["pending"]}</a> {$i18n["status"]["or"]} <a href="/suite/admin/actions/worktime/state_vacation.php?id={$id}&new=approve&u={$rnw}">{$i18n["status"]["approved"]}</a> {$i18n["status"]["or"]} <a href="/suite/admin/actions/worktime/state_vacation.php?id={$id}&new=reject&u={$rnw}">{$i18n["status"]["rejected"]}</a></td>
                        </tr>


                    DATA;

                    echo $data;
                }
            } else {
                Exceptions::error_rep("[VACATION] No vacations found. Returning.");
                return $i18n["status"]["not_found"];
            }
        }

        public function get_all_vacation()
        {
            Exceptions::error_rep("[VACATION] Getting all vacations...");
            $data = $this->db->sendQuery("SELECT * FROM `vacation`;");
            $data->execute();
            $count = $data->rowCount();

            if($count == 0){
                Exceptions::error_rep("[VACATION] No vacations found. Returning.");
                return false;
            }
            while($row = $data->fetch(\PDO::FETCH_ASSOC)){
                $arr[$row["id"]] = $row;
            }
            return $arr;
        }
    }
}

?>