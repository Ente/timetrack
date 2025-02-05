<?php

namespace Arbeitszeit {
    class MailWorktimeUncompliant extends Auth
    {
        public static function mail_worktime_uncompliant($username, $worktime, $type = 1, \PHPMailer\PHPMailer\PHPMailer $mail, $password = null)
        {
            $i18n = new i18n;
            $loc = $i18n->loadLanguage(null, "emails/worktime/uncompliant");
            $base_url = Arbeitszeit::get_app_ini()["general"]["base_url"];
            $conn = new DB;
            $sql = "SELECT * FROM `users` WHERE username = ?;";
            $res = $conn->sendQuery($sql);
            $res->execute([$username]);
            $count = $res->rowCount();
            $ii = Arbeitszeit::get_app_ini()["general"]["app_name"];

            if ($count == 1) {
                $data = $res->fetch(\PDO::FETCH_ASSOC);
            } else {
                Exceptions::error_rep("An error occured while fetching user data from database for user '$username'. See previous message for more information.");
                return [
                    "error" => [
                        "error_code" => 10,
                        "error_message" => "Error while fetching user data (results > 1 or 0)"
                    ]
                ];
            }

            $sql1 = "SELECT * FROM `arbeitszeiten` WHERE id = ?;";
            $res1 = $conn->sendQuery($sql);
            $res1->execute([$worktime]);
            if ($res1 != false) {
                $worktime_data = $res1->fetch(\PDO::FETCH_ASSOC);
            } else {
                Exceptions::error_rep("An error occured while fetching worktime data from database for id '{$worktime}'. See previous message for more information.");
                return [
                    "error" => [
                        "error_code" => 11,
                        "error_message" => "Error while fetching worktime data"
                    ]
                ];
            }
            if ($type == 1) {

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

                <p>{$loc["message1"]}:

                <table>
                    <tr>
                        <th>{$loc["id"]}</th>
                        <th>{$loc["username"]}</th>
                        <th>{$loc["day"]}</th>
                        <th>{$loc["begin"]}</th>
                        <th>{$loc["send"]}</th>
                        <th>{$loc["notes"]}</th>
                    <tr>
                    <tr>
                        <td>{$worktime_data["id"]}</td>
                        <td>{$worktime_data["username"]}</td>
                        <td>{$worktime_data["schicht_tag"]}</td>
                        <td>{$worktime_data["schicht_anfang"]}</td>
                        <td>{$worktime_data["schicht_ende"]}</td>
                        <td>{$worktime_data["ort"]}</td>
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
                $mail->Subject = "{$loc["subject1"]} | {$ii}";
                $mail->Body = $text;
                $mail->isHTML(true);
                #try {
                $mail->send();
                #} catch (\Exception $e){
                #  echo "Error: {$mail->ErrorInfo}";
                #}
                Exceptions::error_rep("An email has been sent to user '$username'. Trigger: Worktime uncompliant");
                return 1;
            } elseif ($type == 0) {
                $text = <<<DATA
                <p>{$loc["greetings"]} {$data["name"]},</p>
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

                <p>{$loc["message2"]}:

                <table>
                    <tr>
                        <th>{$loc["id"]}</th>
                        <th>{$loc["username"]}</th>
                        <th>{$loc["day"]}</th>
                        <th>{$loc["begin"]}</th>
                        <th>{$loc["send"]}</th>
                        <th>{$loc["notes"]}</th>
                    <tr>
                    <tr>
                        <td>{$worktime_data["id"]}</td>
                        <td>{$worktime_data["username"]}</td>
                        <td>{$worktime_data["schicht_tag"]}</td>
                        <td>{$worktime_data["schicht_anfang"]}</td>
                        <td>{$worktime_data["schicht_ende"]}</td>
                        <td>{$worktime_data["ort"]}</td>
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
                $mail->Subject = "{$loc["subject2"]} | {$ii}";
                $mail->Body = $text;
                $mail->isHTML(true);
                #try {
                $mail->send();
                #} catch (\Exception $e){
                #  echo "Error: {$mail->ErrorInfo}";
                #}
                Exceptions::error_rep("An email has been sent to user '$username'. Trigger: Worktime compliant");
                return 1;
            }
        }
    }
}



?>