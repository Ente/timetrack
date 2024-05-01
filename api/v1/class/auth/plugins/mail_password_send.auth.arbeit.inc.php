<?php

namespace Arbeitszeit{
    class MailPasswordSend extends Auth{
        public static function mail_password_send($username, $type = 1, \PHPMailer\PHPMailer\PHPMailer $mail, $password = null){
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
            $token = rand(111111, PHP_INT_MAX); # token just to identify the user
            $email_urlencoded = urlencode($data["email"]);
            #$from = "Password Reset Service (AZES)";
            $subject = "Dein Passwort für {$ii} (AZES)";
            if($type ==1){
                $password = "Dein Passwort wurde von deinem Vorgesetzten festgelegt. Falls du das Passwort nicht mehr kennst, kannst du es zurücksetzen!";
            }
            $text = <<< DATA
            <p>Hallo {$data["name"]},</p>

            <pweiter unten findest du dein Passwort.
            Hierzu wurde zuvor ein Login erstellt, unter welchem du dich unter https://{$base_url}/ anmelden kannst.></p>

            <p>Dein Passwort: "{$password}"</p>
            
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
            $mail->Subject = $subject;
            $mail->Body = $text;
            $mail->isHTML(true);
            #try {
                $mail->send();
            #} catch (\Exception $e){
              #  echo "Error: {$mail->ErrorInfo}";
            #}
            Exceptions::error_rep("An email has been sent to user '$username'. Trigger: User Creation Initial Password");
            return 1;
        }
    }
}




?>