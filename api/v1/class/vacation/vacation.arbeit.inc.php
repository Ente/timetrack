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

        public function add_vacation($start, $stop)
        {
            $conn = $this->get_conn();
            $user = $_SESSION["username"];
            $dateString = $start;
            $format = 'Y-m-d';

            $dateObj = \DateTime::createFromFormat($format, $dateString);

            if ($dateObj == false) {
              Exceptions::error_rep("[VACATION] An error occured while adding an vacation for user '$user'. Could not validate dateFormat! | String: '{$dateString}', expected: d-m-Y");
              return false;   
            }

            $sql = "INSERT INTO `vacation` (`id`, `username`, `start`, `stop`, `status`) VALUES ('0', '{$user}', '{$start}', '{$stop}', 'pending') ";
            $query = mysqli_query($conn, $sql);
            if(mysqli_error($conn)){
                Exceptions::error_rep("[VACATION] An error occured while adding an vacation for user '$user'. | Error: " . mysqli_error($conn));
                return false;
            } else {
                Exceptions::error_rep("[VACATION] Successfully added vacation for user '$user'.");
                return true;
            }
        }

        public function remove_vacation($id){ # admin function only
            $conn = $this->get_conn();
            $id = mysqli_real_escape_string($conn, $id);
            $query = mysqli_query($conn, "DELETE * FROM `vacation` WHERE id = '{$id}';");

            if(mysqli_error($conn)){
                Exceptions::error_rep("[VACATION] An error occured while deleting a vacation with id '{$id}'. | Error: " . mysqli_error($conn));
                return false;
            }
            Exceptions::error_rep("[VACATION] Successfully removed vacation with id '{$id}'.");
            return true;
        }

        public function change_status($id, $new_state = 3) # admin function only
        {
            $conn = Arbeitszeit::get_conn();
            $id = mysqli_real_escape_string($conn, $id);
            if($new_state == 1 /* approve */){
                $sql = "UPDATE `vacation` SET `status` = 'approved' WHERE `id` = '{$id}';";
            } elseif($new_state == 2 /* rejected */) {
                $sql = "UPDATE `vacation` SET `status` = 'rejected' WHERE `id` = '{$id}';";
            } else {
                $sql = "UPDATE `vacation` SET `status` = 'pending' WHERE `id` = '{$id}';";
            }
            $res = mysqli_query($conn, $sql);
            if($res == false){
                Exceptions::error_rep("[VACATION] An error occured while setting status for vacaction. id '{$id}', new state: '{$new_state}' | SQL-Error: " . mysqli_error($conn));
                return false;
            } else {
                Exceptions::error_rep("[VACATION] Successfully changed status for vacation id '{$id}', new state: '{$new_state}'.");
                return true;
            }

        }

        public function get_vacation($id, $mode = 1){
            $conn = Arbeitszeit::get_conn();
            $id = mysqli_real_escape_string($conn, $id);
            $sql = "SELECT * FROM `vacation` WHERE id = '{$id}'";
            $res = mysqli_query($conn, $sql);
            if($res == false){
                Exceptions::error_rep("[VACATION] An error occured while getting vacaction. id '{$id}' | SQL-Error: " . mysqli_error($conn));
                return false;
            } else {
                Exceptions::error_rep("[VACATION] Successfully found vacation id '{$id}' inside database.");
                if($mode == 2){
                    return true;
                } else {
                    return mysqli_fetch_assoc($res);
                }
            }
        }

        public function display_vacation_all(){ # admin function only
            $conn = $this->get_conn();
            $sql = "SELECT * FROM `vacation` LIMIT 100;";
            $res = mysqli_query($conn, $sql);

            if(@mysqli_num_rows($res) > 0){
                # compute and return data
                while($row = \mysqli_fetch_assoc($res)){
                    $rnw = $row["username"];
                    $start = strftime("%d.%m.%Y", strtotime($row["start"]));
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
                        case "rejected":
                            $status = "<span style='color:red'>rejected</span>";
                            break;
                    }

                    if($stop = "01.01.1970"){
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
    }
}

?>