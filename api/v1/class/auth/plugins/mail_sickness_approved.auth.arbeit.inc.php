<?php

namespace Arbeitszeit {
    class MailSicknessApproved extends Auth
    {
        public static function mail_sickness_approved($username, $id, \PHPMailer\PHPMailer\PHPMailer $mail, $password = null)
        {
            $base_url = Arbeitszeit::get_app_ini()["general"]["base_url"];
            $conn = Arbeitszeit::get_conn();
            $sql = "SELECT * FROM `users` WHERE username = '{$username}';";
            $res = mysqli_query($conn, $sql);
            $count = mysqli_num_rows($res);
            $ii = Arbeitszeit::get_app_ini()["general"]["app_name"];

            if ($count == 1) {
                $data = mysqli_fetch_assoc($res);
            } else {
                Exceptions::error_rep("An error occured while fetching user data from database for user '$username' | SQL-Error: " . mysqli_error($conn));
                return [
                    "error" => [
                        "error_code" => 10,
                        "error_message" => "Error while fetching user data (results > 1 or 0)"
                    ]
                ];
            }

            $sql1 = "SELECT * FROM `sickness` WHERE id = '{$id}';";
            $res1 = mysqli_query($conn, $sql1);
            if ($res1 != false) {
                $worktime_data = mysqli_fetch_assoc($res1);
            } else {
                Exceptions::error_rep("An error occured while fetching sickness data from database for id '{$id}' | SQL-Error: " . mysqli_error($conn));
                return [
                    "error" => [
                        "error_code" => 11,
                        "error_message" => "Error while fetching worktime data"
                    ]
                ];
            }

            $text = <<<DATA
                <style>
  table {
    width: 100%;
    border-collapse: collapse;
  }

  th, td {
    border: 1px solid #dddddd;
    text-align: left;
    padding: 8px;
  }

  th {
    background-color: #f2f2f2;
  }

  tr:nth-child(even) {
    background-color: #f2f2f2;
  }
</style>
                <p>Hallo {$data["name"]},</p>

                <p>dein Vorgesetzter hat deine Krankheit <span style="color:green;">genehmigt</span>:

                <table>
                    <tr>
                        <th>ID</th>
                        <th>Benutzername</th>
                        <th>Tag</th>
                        <th>Beginn</th>
                        <th>Ende</th>
                    <tr>
                    <tr>
                        <td>{$worktime_data["id"]}</td>
                        <td>{$worktime_data["username"]}</td>
                        <td>{$worktime_data["date_start"]}</td>
                        <td>{$worktime_data["date_end"]}</td>
                        <td>{$worktime_data["status"]}</td>
                    </tr>
                </table>
            <br>
            <p>Mit freundlichen Grüßen</p>
            <br>
            <b>(automatischer Absender)</b>

            <hr>

            <i>Sie erhalten die Email, da Ihr Arbeitgeber Sie für dieses System angemeldet hat und Sie den Email-Benachrichtigungen zugestimmt haben.
            Unter https://{$base_url}/privacy_policy können Sie die aktuelle Datenschutzgrundverordnung einsehen.</i>

            <span style="color:red"><b>Vertrauliche Informationen!</b></span>
DATA;
            $mail->CharSet = "UTF-8";
            $mail->Encoding = "base64";
            $mail->Subject = "Deine Krankheit wurde genehmigt!";
            $mail->Body = $text;
            $mail->isHTML(true);
            #try {
            $mail->send();
            #} catch (\Exception $e){
            #  echo "Error: {$mail->ErrorInfo}";
            #}
            Exceptions::error_rep("An email has been sent to user '$username'. Trigger: Sickness approved");
            return 1;
        }
    }
}

?>