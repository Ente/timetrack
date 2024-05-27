<?php
namespace Arbeitszeit {
    use Arbeitszeit\Arbeitszeit;

    class Mailbox extends Arbeitszeit
    {

        public function __construct(){
            header("HTTP/1.0 403 Forbidden");
            header("Location: /errors/403.html");
        }
        public function create_mailbox_entry($name, $description, $user){
            $conn = Arbeitszeit::get_conn();

            $name = mysqli_real_escape_string($conn, $name);
            $description = mysqli_real_escape_string($conn, $description);

            
            
            # check user
            if(!Benutzer::get_user($user)){
                return false;
            }

            $sql = "INSERT INTO `mailboxes` (`name`, `description`, `file`, `user`, `seen`) VALUES ('{$name}', '{$description}', NULL, '{$user}', '0');";
            $res = mysqli_query($conn, $sql);
            if($res != false){
                Exceptions::error_rep("An error occured while creating an mailbox entry. | SQL-Error: " . mysqli_error($conn));
                return true;
            }
        }

        public function create_mailbox_file_entry($data, $id){
            if(isset($data["url"])){
                $data["url"] = mysqli_real_escape_string(Arbeitszeit::get_conn(), $data["url"]);
                $sql = "INSERT INTO `mailboxes_files` (`m_id`, `name`, `type`, `url`, `username`) VALUES ('{$id}', 'File', 'url', '{$data["url"]}', 'admin');";
            }

            $res = mysqli_query(Arbeitszeit::get_conn(), $sql);
            if($res != false){
                return true;
            } else {
                Exceptions::error_rep("An error occured while creating an file attachement for an mailbox entry id '$id' -. | SQL-Error: " . mysqli_error($conn) . " | supplied data: $data");
                return false;
            }
        }
        public function get_user_mailbox_html_list($user)
        {
            $conn = Arbeitszeit::get_conn();
            $sql = "SELECT * FROM `mailboxes` WHERE user = '{$user}';";
            $res = mysqli_query($conn, $sql);
            $base_url = $ini = Arbeitszeit::get_app_ini()["general"]["base_url"];
            if (mysqli_num_rows($res) > 0) {
                while ($row = mysqli_fetch_assoc($res)) {
                    $name = $row["name"];
                    $description = $row["description"];
                    $file = $row["file"];
                    $user = $row["user"];
                    $id = $row["id"];

                    if ($file != null || $file != false) {
                        $r = "&attachement-id={$id}";
                        $rr = "| Anhänge beigefügt!";
                    } else {
                        $r = null;
                        $rr = null;
                    }

                    return $t = <<<DAT

                    <tr>
                        <td>$name</td>
                        <td>$description $rr</td>
                        <td><a href="http://{$base_url}/suite/mailbox/view_entry.php?&id=$id{$r}" target="_blank">Öffnen</a></td>
                    </tr>

                    DAT;


                }
            } else {
                return "Keine Mailbox-Einträge verfügbar.";
            }
        }

        public function get_user_mailbox_entry($id)
        {
            $sql = "SELECT * FROM `mailboxes` WHERE id = '{$id}';";
            $res = mysqli_query(Arbeitszeit::get_conn(), $sql);
            if (mysqli_num_rows($res) >= 1) {
                return mysqli_fetch_assoc($res);
            } else {
                Exceptions::error_rep("An error occured while retrieving an mailbox entry for id '$id'. | SQL-Error: " . mysqli_error($conn));
                return false;
            }
        }

        private function trigger_seen_entry($id){
            $sql = "UPDATE `mailboxes` SET `seen` = '1' WHERE `mailboxes`.`id` = '{$id}';";
            $res = mysqli_query(Arbeitszeit::get_conn(), $sql);
            if($res == true){
                return true;
            } else {
                Exceptions::error_rep("An error occured while setting trigger for seen an mailbox entry for id '$id'. | SQL-Error: " . mysqli_error($conn));
                return false;
            }
        }

        public function get_user_mailbox_entry_attachement($id)
        {
            $sql = "SELECT * FROM `mailboxes_files` WHERE id = '{$id}';";
            $conn = Arbeitszeit::get_conn();
            $res = mysqli_query($conn, $sql);
            if (mysqli_num_rows($res) >= 1) {
                $data = mysqli_fetch_assoc($res);
                return $data;
            } else {
                return false;
            }
        }

        public function display_mailbox_attachement_html($att_data)
        {

            switch ($att_data["type"]) {
                case "png":
                case "jpeg":
                case "jpg":
                    $preview = "<button class='button-blur' onclick='revealImage()'>Anzeigen</button><img src='{$att_data["url"]}' class='blur' id='hiddenImage' width='100%'></button><script>function revealImage(){document.getElementById('hiddenImage').classList.remove('blur');}</script>";
                    break;
                case "pdf":
                    $preview = "<button class='button-blur' onclick='revealImage()'>Anzeigen</button><iframe src='{$att_data["url"]}' class='blur' width='100%' height='500px' id='hiddenImage'></iframe><script>function revealImage(){document.getElementById('hiddenImage').classList.remove('blur');}</script>";
                    break;
                default:
                    $preview = "<em>Preview not available.</em>";
            }

            $b = <<<DATA

            <hr>

            <p>Anhang: {$att_data["name"]} | Typ: {$att_data["type"]}</p>
            <a href="{$att_data["url"]}" target="_blank">Anhang öffnen</a>

            <br>
            <p>Preview: </p>
            <div class="box">{$preview}</div>
            
DATA;
            return $b;
        }

        public function display_mailbox_entry_html($data)
        {
            if($data["seen"] == 0){
                $this->trigger_seen_entry($data["id"]);
                $e = " | <em style='font-size:x-small'>Gesehen</em>";
            } else {
                $e = " | <em style='font-size:x-small'>Gesehen</em>";
            }
            return <<<DATA

            <h1>{$data["name"]}</h1>
                {$data["description"]}
                $e
            
DATA;
        }

        public function get_user_mailbox_entry_f($id, $a_id = null)
        {
            $data = $this->get_user_mailbox_entry($id);
            if ($data == false || $data["user"] != $_SESSION["username"]) {
                return "Canceling due to incorrect id.";
            }
            if ($a_id != null) {
                $att = $this->get_user_mailbox_entry_attachement($a_id);
                if ($att != false || $att != null) {
                    return [
                        "letter" => $this->display_mailbox_entry_html($data),
                        "attachement" => $this->display_mailbox_attachement_html($att)
                    ];

                } else {
                    Exceptions::error_rep("An error occured while retrieving attachement an mailbox entry. | SQL-Error: " . mysqli_error($conn));
                    return "Could not get mailbox entry attachement.";
                }
            }
        }
        public function get_specific_mailbox_html(int $from = 1)
        {   
            $mf = $from+50;
            $conn = Arbeitszeit::get_conn();
            $base_url = $ini = Arbeitszeit::get_app_ini()["general"]["base_url"];
            $sql = "SELECT * FROM `mailboxes` WHERE id BETWEEN {$from} AND {$mf} ORDER BY id DESC;";
            $res = mysqli_query($conn, $sql);
            if (mysqli_num_rows($res) > 0) {
                while ($row = mysqli_fetch_assoc($res)) {
                    $id = $row["id"];
                    $name = $row["name"];
                    $file = $row["file"];
                    $user = $row["user"];
                    $seen = $row["seen"];
                    if($file != null){
                        $file = "Ja";
                    } else {
                        $file = "Nein";
                    }

                    if($seen == 0){
                        $seen = "Nein";
                    } else {
                        $seen = "Ja";
                    }

                    $data = <<<DATA

                        <tr>
                            <td><a href="http://{$base_url}/suite/admin/actions/mailbox/delete.php?id={$id}">Eintrag löschen</a></td>
                            <td>$id</td>
                            <td>$name</td>
                            <td>$file</td>
                            <td>$user</td>
                            <td>$seen</td>
                        </tr>


                        DATA;


                    return $data;

                }
            } else {
                return "Keine Mailbox-Einträge verfügbar.";
            }

        }

    }
}


?>