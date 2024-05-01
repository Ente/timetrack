<?php
namespace Arbeitszeit{
    class pdf extends Arbeitszeit{
        public function get_specific_worktime_pdf($user, $month, $year){
            $conn = Arbeitszeit::get_conn();
            $ini = Arbeitszeit::get_app_ini();
            $hours = $this->calculate_hours_specific_time($user, $month, $year);
            if(is_string($year) != true){
                $year = date("Y");
            }
            $sql = "SELECT * FROM `arbeitszeiten` WHERE YEAR(schicht_tag) = '{$year}' AND MONTH(schicht_tag) = '{$month}' AND username = '{$user}' ORDER BY schicht_tag DESC;";
            $res = mysqli_query($conn, $sql);
            if(mysqli_error($conn)){
                Exceptions::error_rep("An error occured while generating worktime pdf. | SQL-Error: " . mysqli_error($conn));
                die(mysqli_error($conn));
            }
            $user_data = Benutzer::get_user($user);
            $user_data["name"] ?? mysqli_fetch_assoc($res)[0]["name"]; # Bug 14 Fix -> http://bugzilla.openducks.org/show_bug.cgi?id=14

            $data = <<< DATA
            <html>
                <body style="text-align:center; font-family: Arial;">
                    <h1>Arbeitszeiten von <b>{$user_data["name"]}</b></h1>
                    <style>
                        .box {
                            width: auto;
                            max-width: 900px;
                            height: auto;
                            border: 5px solid;
                            padding: auto;
                            margin: auto;
                            border-radius: 5px;
                            margin-left: auto;
                            margin-right: auto;
                            opacity: 1;
                            border-color: rgba(255,255,255,0.64);
                            transition: all 0.5s;
                    </style>
                    <div class="box">
                        <table style="width:100%;border:solid;">
                            <tr>
                                <th>Tag</th>
                                <th>Uhrzeit Anfang</th>
                                <th>Uhrzeit Ende</th>
                                <th>Pause Start</th>
                                <th>Pause Ende</th>
                                <th>Ort</th>
                            </tr>

            DATA;
            if(mysqli_num_rows($res) > 0){
                $q = mysqli_num_rows($res);
                $i = 0;
                while($row = mysqli_fetch_assoc($res)){
                    if($i == 0){
                        $r = strftime("%d.%m.%Y", strtotime($row["schicht_tag"]));
                        
                    }
                    $i++;
                    #$rnw = $row["name"];
                    $raw = strftime("%d.%m.%Y", strtotime($row["schicht_tag"]));
                    $rew = $row["schicht_anfang"];
                    $rol = $row["schicht_ende"];
                    $ral = $row["ort"];
                    $rps = strftime("%H:%M", strtotime($row["pause_start"]));
                    $rpe = strftime("%H:%M", strtotime($row["pause_end"]));

                    
                    if($rps == "01:00" ||$rps == null){
                        $rps = "-";
                    }
                    if($rpe == "01:00" || $rpe == null){
                        $rpe = "-";
                    }

                    $data .= <<< DATA

                            <tr>
                                <td>{$raw}</td>
                                <td>{$rew}</td>
                                <td>{$rol}</td>
                                <td>{$rps}</td>
                                <td>{$rpe}</td>
                                <td>{$ral}</td>
                            </tr>


                    DATA;
                    if($i == $q){
                        $s = strftime("%d.%m.%Y", strtotime($row["schicht_tag"]));
                    }
                }
            } else {
                return "<p style='text-align: center;'>Keine Daten gefunden!</p>";
            }
            $data .= <<< DATA

                        </table>
                        <p>Arbeitszeit insgesamt (gerundet): {$hours["hours_rounded"]}</p>
                        <p>Anfangsdatum der Daten: {$s} - Enddatum: {$r}</p>
                    </div>
                </body>
            </html>


            DATA;

            return $data;
        }
    }
}



?>