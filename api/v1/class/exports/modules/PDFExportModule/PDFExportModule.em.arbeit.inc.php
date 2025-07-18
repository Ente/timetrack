<?php
namespace Arbeitszeit\ExportModule;
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\i18n;
use Arbeitszeit\Exceptions;
use Arbeitszeit\Benutzer;

/**
 * PDFExportModule - Allows you to export worktime sheets
 */
class PDFExportModule implements ExportModuleInterface {
    public function export($args) {
            $i18n = new i18n;
            $arbeit = new Arbeitszeit;
            $user = $args["user"];
            $month = $args["month"];
            $year = $args["year"];
            $i18nn = $i18n->loadLanguage(null, "class/pdf");
            $ini = Arbeitszeit::get_app_ini();
            $hours = $arbeit->calculate_hours_specific_time($user, $month, $year);
            if(is_string($year) != true){
                $year = date("Y");
            }
            $sql = "SELECT * FROM `arbeitszeiten` WHERE YEAR(schicht_tag) = ? AND MONTH(schicht_tag) = ? AND username = ? ORDER BY schicht_tag DESC;";
            $statement = $arbeit->db()->sendQuery($sql);
            $userdata = $statement->execute([$year, $month, $user]);
            if($userdata == false){
                Exceptions::error_rep("An error occurred while generating worktime pdf. See previous message for more information");
                die("An error occurred!");
            }
            $user_data = Benutzer::get_user($user);
            $user_data["name"] ?? $statement->fetch(\PDO::FETCH_ASSOC)[0]["name"]; # Bug 14 Fix -> http://bugzilla.openducks.org/show_bug.cgi?id=14
            try {
                $css = @file_get_contents($ini["exports"]["pdf"]["css"]);
            } catch (\ValueError $e) {
                $css = file_get_contents(__DIR__ . "/css/index.css");
            }
            $data = <<< DATA
            <html>
                <body style="text-align:center; font-family: Arial;">
                    <h1>{$i18nn["worktime_note"]} <b>{$user_data["name"]}</b></h1>
                    <style>
                        $css
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
                                <th>{$i18nn["type"]}</th>
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
                    $rtn = $arbeit->type_from_int($row["Wtype"]) ?? "N/A";
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
                                <td>{$rtn}</td>
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

    public function saveAsPdf($args) {
        Exceptions::error_rep("Saving PDF file...", 1, "N/A");
        $html = $this->export($args);  
        $user = $args['user'] ?? "dummy";
        $month = $args['month'] ?? "00";
        $year = $args['year'] ?? "0000";
    
        
        $directory = $_SERVER["DOCUMENT_ROOT"] . "/data/exports/" . $this->getName() . "/$user";
        $filename = "$directory/worktimes_{$year}-{$month}.pdf";
    
        
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
    
        
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
    
        
        file_put_contents($filename, $dompdf->output());
        Exceptions::error_rep("PDF file saved: $filename", 1, "N/A");
        return $filename;
    }

    public function getName() {
        return "PDFExportModule";
    }
    public function getExtension() {
        return "pdf";
    }
    public function getMimeType() {
        return "application/pdf";
    }
    public function getVersion() {
        return "1.0";
    }
    public function geti18n() {}
    public function __set($name, $value) {}
    public function __get($name) {}
    public function __isset($name) {}
    public function __unset($name) {}
    public function __call($name, $arguments) {}
}