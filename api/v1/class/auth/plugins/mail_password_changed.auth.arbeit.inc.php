<?php

namespace Arbeitszeit{
    class MailPasswordChanged extends Auth{
        public static function mail_password_changed($username, \PHPMailer\PHPMailer\PHPMailer $mail){
            $base_url = Arbeitszeit::get_app_ini()["general"]["base_url"];
            $conn = Arbeitszeit::get_conn();
            $sql = "SELECT * FROM `users` WHERE username = '{$username}';";
            $res = mysqli_query($conn, $sql);
            $count = mysqli_num_rows($res);
            $ii = Arbeitszeit::get_app_ini()["general"]["app_name"];

            if($count == 1){
                $data = mysqli_fetch_assoc($res);
            } else {
                Exceptions::error_rep("An error occured while fetching user data from database for user '$username' | SQL-Error: " . mysqli_error($res1));
                return [
                    "error" => [
                        "error_code" => 10,
                        "error_message" => "Error while fetching user data (results > 1 or 0)"
                    ]
                ];
            }
            #$from = "Password Reset Service (AZES)";
            $subject = "Dein Passwort für {$ii} (AZES) wurde zurückgesetzt!";
            $text = <<< DATA
            <p>Hallo {$data["name"]},</p>

            <p>dein Passwort wurde soeben geändert. Solltest du das nicht veranlasst haben, kannst du dein Passwort zurücksetzen unter dem folgenden Link:</p>
            <a href="http://{$base_url}/suite/forgot_password.php">https://{$base_url}/auth/forgot_password.php</a>
            
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
            Exceptions:error_rep("An email has been sent to user '$username'. Trigger: Password Reset Successfully");
            return 1;
        }
    }
}




?>