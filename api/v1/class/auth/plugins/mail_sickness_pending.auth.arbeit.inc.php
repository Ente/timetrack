<?php

namespace Arbeitszeit {
    class MailSicknessPending extends Auth
    {
        public static function mail_sickness_pending($username, $id, \PHPMailer\PHPMailer\PHPMailer $mail, $password = null)
        {
            $i18n = new i18n;
            $loc = $i18n->loadLanguage(null, "emails/sickness/pending");
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

            $sql1 = "SELECT * FROM `vacation` WHERE id = '{$id}';";
            $res1 = mysqli_query($conn, $sql1);
            if ($res1 != false) {
                $worktime_data = mysqli_fetch_assoc($res1);
            } else {
                Exceptions::error_rep("An error occured while fetching vacation data from database for id '{$id}' | SQL-Error: " . mysqli_error($conn));
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
                <p>{$loc["greetings"]} {$data["name"]},</p>

                <p>{$loc["message"]}:</p>
                <p>({$loc["note"]})</p>

                <table>
                    <tr>
                        <th>{$loc["id"]}</th>
                        <th>{$loc["username"]}</th>
                        <th>{$loc["day"]}</th>
                        <th>{$loc["begin"]}</th>
                        <th>{$loc["send"]}</th>
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
            <p>{$loc["end"]}</p>
            <br>
            <b>({$loc["noreply"]})</b>

            <hr>

            <i>{$loc["gdpr"]}: https://{$base_url}/privacy_policy</i>

            <span style="color:red"><b>{$loc["confidential"]}</b></span>
DATA;
            $mail->CharSet = "UTF-8";
            $mail->Encoding = "base64";
            $mail->Subject = "{$loc["subject"]} | {$ii}";
            $mail->Body = $text;
            $mail->isHTML(true);
            #try {
            $mail->send();
            #} catch (\Exception $e){
            #  echo "Error: {$mail->ErrorInfo}";
            #}
            Exceptions::error_rep("An email has been sent to user '$username'. Trigger: Sickness pending");
            return 1;
        }
    }
}

?>