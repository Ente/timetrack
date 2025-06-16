<?php
namespace Arbeitszeit {
    /**
     * Sickness
     * 
     * v1
     * - Added function to add sickness
     */
    use Arbeitszeit\Events\EventDispatcherService;
    use Arbeitszeit\Events\SicknessCreatedEvent;
    use Arbeitszeit\Events\SicknessDeletedEvent;
    use Arbeitszeit\Events\SicknessUpdatedEvent;
    class Sickness extends Arbeitszeit
    {

        public function add_sickness($start, $stop, $user = null)
        {
            if($this->nodes()->checkNode("sickness.inc", "add_sickness") == false){
                return false;
            }
            Exceptions::error_rep("[SICK] Adding sickness for user '{$user}'...");
            $user = $_SESSION["username"];
            $dateString = $start;
            $format = 'Y-m-d';

            $dateObj = \DateTime::createFromFormat($format, $dateString);

            if ($dateObj == false) {
              Exceptions::error_rep("[SICK] An error occurred while adding an sickness for user '$user'. Could not validate dateFormat! | String: '{$dateString}', expected: d-m-Y");
              return false;
            }

            $data = $this->db()->sendQuery("INSERT INTO sick (id, username, start, stop, status) VALUES (0, ?, ?, ?, 'pending')")->execute([$user, $start, $stop]);
            if($data == false){
                Exceptions::error_rep("[SICK] An error occurred while adding an sickness for user '$user'. See previous message for more information.");
                return false;
            } else {
                EventDispatcherService::get()->dispatch(new SicknessCreatedEvent($user, $start, $stop), SicknessCreatedEvent::NAME);
                Exceptions::error_rep("[SICK] Successfully added sickness for user '$user'.");
                return true;
            }
        }

        public function remove_sickness($id){ # admin function only
            if($this->nodes()->checkNode("sickness.inc", "remove_sickness") == false){
                return false;
            }
            Exceptions::error_rep("[SICK] Removing sickness with id '{$id}'...");
            $data = $this->db()->sendQuery("DELETE * FROM sick WHERE id = ?")->execute([$id]);
            if($data == false){
                Exceptions::error_rep("[SICK] An error occurred while deleting a sickness with id '{$id}'. See previous message for more information.");
                return false;
            }
            EventDispatcherService::get()->dispatch(new SicknessDeletedEvent($_SESSION["username"], $id), SicknessDeletedEvent::NAME);
            return true;
        }

        public function change_status($id, $new_state = 3) # admin function only
        {
            if($this->nodes()->checkNode("sickness.inc", "change_status") == false){
                return false;
            }
            Exceptions::error_rep("[SICK] Changing status for sickness id '{$id}' to '{$new_state}'...");
            if($new_state == 1 /* approve */){
                $sql = "UPDATE `sick` SET `status` = 'approved' WHERE `id` = ?;";
            } elseif($new_state == 2 /* rejected */) {
                $sql = "UPDATE `sick` SET `status` = 'rejected' WHERE `id` = ?;";
            } else {
                $sql = "UPDATE `sick` SET `status` = 'pending' WHERE `id` = ?;";
            }
            $data = $this->db()->sendQuery($sql)->execute([$id]);
            if($data == false){
                Exceptions::error_rep("[SICK] An error occurred while setting status for sickness. id '{$id}', new state: '{$new_state}'. See previous message for more information.");
                return false;
            } else {
                EventDispatcherService::get()->dispatch(new SicknessUpdatedEvent($_SESSION["username"], $id, $new_state), SicknessUpdatedEvent::NAME);
                Exceptions::error_rep("[SICK] Successfully changed status for sickness id '{$id}', new state: '{$new_state}'.");
                return true;
            }

        }

        public function display_sickness_all(){ # admin function only
            if($this->nodes()->checkNode("sickness.inc", "display_sickness_all") == false){
                return false;
            }
            Exceptions::error_rep("[SICK] Displaying all sicknesses...");
            $i18n = $this->i18n()->loadLanguage(null, "worktime/sick/all", "admin");
            
            $sql = "SELECT * FROM `sick` LIMIT 100;";
            $data = $this->db()->sendQuery($sql);
            $data->execute();
            $count = $data->rowCount();

            if($count > 0){
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

                    $print = <<< DATA

                        <tr>
                            <td>{$rnw}</td>
                            <td>{$start}</td>
                            <td>{$stop}</td>
                            <td>{$status} | {$i18n["status"]["set_to"]} <a href="/suite/admin/actions/worktime/state_sickness.php?id={$id}&new=pending&u={$rnw}">{$i18n["status"]["pending"]}</a> {$i18n["status"]["or"]} <a href="/suite/admin/actions/worktime/state_sickness.php?id={$id}&new=approve&u={$rnw}">{$i18n["status"]["approved"]}</a> {$i18n["status"]["or"]} <a href="/suite/admin/actions/worktime/state_sickness.php?id={$id}&new=reject&u={$rnw}">{$i18n["status"]["rejected"]}</a></td></td>
                        </tr>


                    DATA;

                    echo $print;
                }
            } else {
                Exceptions::error_rep("[SICK] No sicknesses found inside database. Returning 'not_found'...");
                return $i18n["status"]["not_found"];
            }
        }

        public function get_sickness($id, $mode = 1){
            Exceptions::error_rep("[SICK] Getting sickness id '{$id}'...");
            $sql = "SELECT * FROM `sick` WHERE id = ?";
            $data = $this->db()->sendQuery($sql)->execute([$id]);
            if($data == false){
                Exceptions::error_rep("[SICK] An error occurred while getting sickness. id '{$id}'. See previous message for more information.");
                return false;
            } else {
                Exceptions::error_rep("[SICK] Successfully found sickness id '{$id}' inside database.");
                if($mode == 2){
                    return true;
                } else {
                    return $data;
                }
            }
        }
    }
}

?>