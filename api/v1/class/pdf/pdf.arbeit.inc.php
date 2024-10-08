<?php
namespace Arbeitszeit{
    class pdf extends Arbeitszeit{
        public function get_specific_worktime_pdf($user, $month, $year){
            $i18n = new i18n;
            $i18nn = $i18n->loadLanguage(null, "class/pdf");
            $ini = Arbeitszeit::get_app_ini();
            $hours = $this->calculate_hours_specific_time($user, $month, $year);
            if(is_string($year) != true){
                $year = date("Y");
            }
            $sql = "SELECT * FROM `arbeitszeiten` WHERE YEAR(schicht_tag) = ? AND MONTH(schicht_tag) = ? AND username = ? ORDER BY schicht_tag DESC;";
            $statement = $this->db()->sendQuery($sql);
            $userdata = $statement->execute([$year, $month, $user]);
            if($userdata == false){
                Exceptions::error_rep("An error occured while generating worktime pdf. See previous message for more information");
                die("An error occured!");
            }
            $user_data = Benutzer::get_user($user);
            $user_data["name"] ?? $statement->fetch(\PDO::FETCH_ASSOC)[0]["name"]; # Bug 14 Fix -> http://bugzilla.openducks.org/show_bug.cgi?id=14

            $data = <<< DATA
            <html>
                <body style="text-align:center; font-family: Arial;">
                    <h1>{$i18nn["worktime_note"]} <b>{$user_data["name"]}</b></h1>
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
                                <th>{$i18nn["day"]}</th>
                                <th>{$i18nn["tbegin"]}</th>
                                <th>{$i18nn["tend"]}</th>
                                <th>{$i18nn["pbegin"]}</th>
                                <th>{$i18nn["pend"]}</th>
                                <th>{$i18nn["loc"]}</th>
                            </tr>

            DATA;
            if($statement->rowCount() > 0){
                $q = $statement->rowCount();
                $i = 0;
                while($row = $statement->fetch(\PDO::FETCH_ASSOC)){
                    if($i == 0){
                        $r = @strftime("%d.%m.%Y", strtotime($row["schicht_tag"]));
                        
                    }
                    $i++;
                    #$rnw = $row["name"];
                    $raw = @strftime("%d.%m.%Y", strtotime($row["schicht_tag"]));
                    $rew = $row["schicht_anfang"];
                    $rol = $row["schicht_ende"];
                    $ral = $row["ort"];
                    $rps = @strftime("%H:%M", strtotime($row["pause_start"]));
                    $rpe = @strftime("%H:%M", strtotime($row["pause_end"]));

                    
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
                        $s = @strftime("%d.%m.%Y", strtotime($row["schicht_tag"]));
                    }
                }
            } else {
                return "<p style='text-align: center;'>{$i18nn["no_data"]}</p>";
            }
            $data .= <<< DATA

                        </table>
                        <p>{$i18nn["worktime_all"]}: {$hours["hours_rounded"]}</p>
                        <p>{$i18nn["worktime_date"]}: {$s} - {$i18nn["end_date"]}: {$r}</p>
                    </div>
                </body>
            </html>
            <script>window.print()</script>


            DATA;

            return $data;
        }
    }
}



?>