<?php

namespace Arbeitszeit{
    class NewMailEntry extends Auth{
        public static function mail_new_entry($username, $mailid, \PHPMailer\PHPMailer\PHPMailer $mail){
            $base_url = Arbeitszeit::get_app_ini()["general"]["base_url"];
            $conn = Arbeitszeit::get_conn();
            $sql = "SELECT * FROM `mailboxes` WHERE id = '{$mailid}';";
            $res = mysqli_query($conn, $sql);
            $count = mysqli_num_rows($res);
            $ii = Arbeitszeit::get_app_ini()["general"]["app_name"];

            $sql = "SELECT * FROM `users` WHERE username = '$username';";
            $res1 = mysqli_query($conn, $sql);
            $count1 = mysqli_num_rows($res1);
            if($count1 == 1){
                $data1 = mysqli_fetch_assoc($res1);
            } else {
                Exceptions::error_rep("An error occured while fetching user data from database for user '$username' for mailbox entry creation ID '$mailid' | SQL-Error: " . mysqli_error($conn));
                return [
                    "error" => [
                        "error_code"=> 10,
                        "error_message" => "Error while fetching user data"
                    ]
                    ];
            }

            if($count == 1){
                $data = mysqli_fetch_assoc($res);
            } else {
                Exceptions::error_rep("An error occured while fetching mailbox data from database for user '$username' for mailbox entry creation ID '$mailid' | SQL-Error: " . mysqli_error($conn));
                return [
                    "error" => [
                        "error_code" => 10,
                        "error_message" => "Error while fetching user data (results > 1 or 0)"
                    ]
                ];
            }
            if ($data["file"] != null) {
                $r = "&attachement-id={$data["id"]}";
                $rr = "| Anhänge beigefügt!";
            } else {
                $r = null;
                $rr = null;
            }
            #$from = "Password Reset Service (AZES)";
            $subject = "Neue Benachrichtigung in deinem digitalen Postfach!";
            $text = <<< DATA
            <p>Hallo {$data1["name"]},</p>

            <p>dein Passwort wurde soeben geändert. Solltest du das nicht veranlasst haben, kannst du dein Passwort zurücksetzen unter dem folgenden Link:</p>
            <a href="http://{$base_url}/suite/mailbox/view_entry.php?id={$data["id"]}{$r}">https://{$base_url}/auth/forgot_password.php</a>
            
            <br>
            <p>Mit freundlichen Grüßen</p>
            <br>
            <b>(automatischer Absender)</b>

            <hr>

            <i>Sie erhalten die Email, da Ihr Arbeitgeber Sie für dieses System angemeldet hat und Sie den Email-Benachrichtigungen zugestimmt haben.
            Unter https://{$base_url}/privacy_policy können Sie die aktuelle Datenschutzgrundverordnung einsehen.</i>

        DATA;
            $mail->CharSet = "UTF-8";
            $mail->Encoding = "base64";
            $mail->Subject = $subject;
            $mail->Body = $text;
            $mail->isHTML(true);
            #try {
                $mail->send();
            #} catch (\Exception $e){
              #  echo "Error: {$mail->ErrorInfo}";
            #}
            Exceptions::error_rep("An email has been sent to user '$username'. Trigger: New Notification recieved.");
            return 1;
        }
    }
}




?>