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
            return true;
        }

        public function change_status() # admin function only
        {

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
                    $stop = @strftime("%d.%m.%Y", strtotime($row["end"]));
                    $status = $row["status"];

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

                    $data = <<< DATA

                        <tr>
                            <td>{$rnw}</td>
                            <td>{$start}</td>
                            <td>{$stop}</td>
                            <td>{$status}</td>
                        </tr>


                    DATA;

                    echo $data;
                }
            } else {
                return "Keine Urlaube eingetragen.";
            }
        }
    }
}

?>