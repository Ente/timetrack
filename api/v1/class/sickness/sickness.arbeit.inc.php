<?php
namespace Arbeitszeit {
    /**
     * Sickness
     * 
     * v1
     * - Added function to add sickness
     */
    class Sickness extends Arbeitszeit
    {

        public function add_sickness($start, $stop, $user = null)
        {
            $user = $_SESSION["username"];
            $dateString = $start;
            $format = 'Y-m-d';

            $dateObj = \DateTime::createFromFormat($format, $dateString);

            if ($dateObj == false) {
              Exceptions::error_rep("[SICK] An error occured while adding an sickness for user '$user'. Could not validate dateFormat! | String: '{$dateString}', expected: d-m-Y");
              return false;   
            }

            $data = $this->db()->sendQuery("INSERT INTO sick (id, username, start, stop, status) VALUES (0, ?, ?, ?, 'pending')")->execute([$user, $start, $stop]);
            if($data == false){
                Exceptions::error_rep("[SICK] An error occured while adding an sickness for user '$user'. See previous message for more information.");
                return false;
            } else {
                Exceptions::error_rep("[SICK] Successfully added sickness for user '$user'.");
                return true;
            }
        }

        public function remove_sickness($id){ # admin function only
            $data = $this->db()->sendQuery("DELETE * FROM sick WHERE id = ?")->execute([$id]);
            if($data == false){
                Exceptions::error_rep("[SICK] An error occured while deleting a sickness with id '{$id}'. See previous message for more information.");
                return false;
            }
            return true;
        }

        public function change_status($id, $new_state = 3) # admin function only
        {
            if($new_state == 1 /* approve */){
                $sql = "UPDATE `vacation` SET `status` = 'approved' WHERE `id` = ?;";
            } elseif($new_state == 2 /* rejected */) {
                $sql = "UPDATE `vacation` SET `status` = 'rejected' WHERE `id` = ?;";
            } else {
                $sql = "UPDATE `vacation` SET `status` = 'pending' WHERE `id` = ?;";
            }
            $data = $this->db()->sendQuery($sql)->execute([$id]);
            if($data == false){
                Exceptions::error_rep("[VACATION] An error occured while setting status for vacaction. id '{$id}', new state: '{$new_state}'. See previous message for more information.");
                return false;
            } else {
                Exceptions::error_rep("[VACATION] Successfully changed status for vacation id '{$id}', new state: '{$new_state}'.");
                return true;
            }

        }

        public function display_sickness_all(){ # admin function only
            $sql = "SELECT * FROM `sick` LIMIT 100;";
            $data = $this->db()->sendQuery($sql);
            $data->execute();
            $count = $data->rowCount();

            if($count > 0){
                # compute and return data
                while($row = $data->fetch(\PDO::FETCH_ASSOC)){
                    $rnw = $row["username"];
                    $start = @strftime("%d.%m.%Y", strtotime($row["start"]));
                    $stop = @strftime("%d.%m.%Y", strtotime($row["stop"]));
                    $status = $row["status"];
                    $id = $row["id"];

                    switch($status){
                        case "pending":
                            $status = "<span style='color:yellow;'>pending</span>";
                            break;
                        case "approved":
                            $status = "<span style='color:green;'>approved</span>";
                            break;
                        case "action":
                            $status = "<span style='color:red'>Action needed</span>";
                            break;
                    }

                    if($stop = "01.01.1970"){
                        $stop = "-";
                    }

                    $print = <<< DATA

                        <tr>
                            <td>{$rnw}</td>
                            <td>{$start}</td>
                            <td>{$stop}</td>
                            <td>{$status} | Set to <a href="/suite/admin/actions/worktime/state_sickness.php?id={$id}&new=pending&u={$rnw}">Pending</a> or <a href="/suite/admin/actions/worktime/state_sickness.php?id={$id}&new=approve&u={$rnw}">Approved</a> or <a href="/suite/admin/actions/worktime/state_sickness.php?id={$id}&new=reject&u={$rnw}">Rejected</a></td></td>
                        </tr>


                    DATA;

                    echo $print;
                }
            } else {
                return "Keine Krankheiten eingetragen.";
            }
        }

        public function get_sickness($id, $mode = 1){
            $sql = "SELECT * FROM `sickness` WHERE id = ?";
            $data = $this->db()->sendQuery($sql)->execute([$id]);
            if($data == false){
                Exceptions::error_rep("[SICKNESS] An error occured while getting sickness. id '{$id}'. See previous message for more information.");
                return false;
            } else {
                Exceptions::error_rep("[SICKNESS] Successfully found sickness id '{$id}' inside database.");
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