<?php
namespace Arbeitszeit {
    /**
     * vacation
     * 
     * v1
     * - Added function to add vacation
     */
    class Vacation extends Arbeitszeit
    {

        public $db;

        public function __construct(){
            $this->db = new DB;
        }

        public function add_vacation($start, $stop, $username = null)
        {
            if($username != null){
                $user = $_SESSION["username"];
            }
            $dateString = $start;
            $format = 'Y-m-d';

            $dateObj = \DateTime::createFromFormat($format, $dateString);

            if ($dateObj == false) {
              Exceptions::error_rep("An error occured while adding an vacation for user '$user'. Could not validate dateFormat! | String: '{$dateString}', expected: d-m-Y");
              return false;
            }
            $sql = "INSERT INTO `vacation` (`id`, `username`, `start`, `stop`, `status`) VALUES ('0', ?, ?, ?, 'pending') ";
            $data = $this->db->sendQuery($sql)->execute([$user, $start, $stop]);
            if(!$data){
                Exceptions::error_rep("An error occured while adding an vacation for user '$user'. See previous message for more information.");
                return false;
            } else {
                Exceptions::error_rep("Successfully added vacation for user '$user'.");
                return true;
            }
        }

        public function remove_vacation($id){ # admin function only
            $sql = "DELETE * FROM `vacation` WHERE id = ?";
            $data = $this->db->sendQuery($sql)->execute(array([$id]));
            if($data == false){
                Exceptions::error_rep("[VACATION] An error occured while deleting a vacation with id '{$id}'. See previous message for more information");
                return false;
            }
            Exceptions::error_rep("[VACATION] Successfully removed vacation with id '{$id}'.");
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
            $data = $this->db->sendQuery($sql)->execute(array([$id]));
            if($data == false){
                Exceptions::error_rep("[VACATION] An error occured while setting status for vacaction. id '{$id}', new state: '{$new_state}'. See previous message for more information.");
                return false;
            } else {
                Exceptions::error_rep("[VACATION] Successfully changed status for vacation id '{$id}', new state: '{$new_state}'.");
                return true;
            }

        }

        public function get_vacation($id, $mode = 1){
            $data = $this->db->sendQuery("SELECT * FROM `vacation` WHERE id = ?")->execute([$id]);
            if($data == false){
                Exceptions::error_rep("[VACATION] An error occured while getting vacaction. id '{$id}'. See previous message for more information.");
                return false;
            } else {
                Exceptions::error_rep("[VACATION] Successfully found vacation id '{$id}' inside database.");
                if($mode == 2){
                    return true;
                } else {
                    return $data;
                }
            }
        }

        public function display_vacation_all(){ # admin function only
            $sql = "SELECT * FROM `vacation`";
            $data = $this->db->sendQuery($sql);
            $data->execute();
            $count = $data->rowCount();
            if($count > 1){
                # compute and return data
                foreach($data->fetchAll(\PDO::FETCH_ASSOC) as $row){
                    $rnw = $row["username"];
                    $start = strftime("%d.%m.%Y", strtotime($row["start"]));
                    $stop = strftime("%d.%m.%Y", strtotime($row["stop"]));
                    $status = $row["status"];
                    $id = $row["id"];

                    switch($status){
                        case "pending":
                            $status = "<span style='color:yellow;'>pending</span>";
                            break;
                        case "approved":
                            $status = "<span style='color:green;'>approved</span>";
                            break;
                        case "rejected":
                            $status = "<span style='color:red'>rejected</span>";
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
                            <td>{$status} | Set to <a href="/suite/admin/actions/worktime/state_vacation.php?id={$id}&new=pending&u={$rnw}">Pending</a> or <a href="/suite/admin/actions/worktime/state_vacation.php?id={$id}&new=approve&u={$rnw}">Approved</a> or <a href="/suite/admin/actions/worktime/state_vacation.php?id={$id}&new=reject&u={$rnw}">Rejected</a></td>
                        </tr>


                    DATA;

                    echo $data;
                }
            } else {
                Exceptions::error_rep("[VACATION] No vacations found. Returning.");
                return "Keine Urlaube eingetragen.";
            }
        }

        public function get_all_vacation()
        {
            $data = $this->db->sendQuery("SELECT * FROM `vacation`;");
            $data->execute();
            $count = $data->rowCount();

            if($count == 0){
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