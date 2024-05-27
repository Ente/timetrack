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

        public function add_sickness($start, $stop)
        {
            $conn = $this->get_conn();
            $user = $_SESSION["username"];
            $dateString = $start;
            $format = 'Y-m-d';

            $dateObj = \DateTime::createFromFormat($format, $dateString);

            if ($dateObj == false) {
              Exceptions::error_rep("[SICK] An error occured while adding an sickness for user '$user'. Could not validate dateFormat! | String: '{$dateString}', expected: d-m-Y");
              return false;   
            }

            $sql = "INSERT INTO `sick` (`id`, `username`, `start`, `stop`, `status`) VALUES ('0', '{$user}', '{$start}', '{$stop}', 'pending') ";
            $query = mysqli_query($conn, $sql);
            if(mysqli_error($conn)){
                Exceptions::error_rep("[SICK] An error occured while adding an sickness for user '$user'. | Error: " . mysqli_error($conn));
                return false;
            } else {
                Exceptions::error_rep("[SICK] Successfully added sickness for user '$user'.");
                return true;
            }
        }

        public function remove_sickness($id){ # admin function only
            $conn = $this->get_conn();
            $id = mysqli_real_escape_string($conn, $id);
            $query = mysqli_query($conn, "DELETE * FROM `sick` WHERE id = '{$id}';");

            if(mysqli_error($conn)){
                Exceptions::error_rep("[SICK] An error occured while deleting a sickness with id '{$id}'. | Error: " . mysqli_error($conn));
                return false;
            }
            return true;
        }

        public function change_status() # admin function only
        {

        }

        public function display_sickness_all(){ # admin function only
            $conn = $this->get_conn();
            $sql = "SELECT * FROM `sick` LIMIT 100;";
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
                return "Keine Krankheiten eingetragen.";
            }
        }
    }
}

?>