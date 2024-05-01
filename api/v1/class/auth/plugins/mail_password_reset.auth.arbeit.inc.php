<?php

namespace Arbeitszeit{
    class MailPasswordReset extends Auth{
        public static function mail_password_reset($username, \PHPMailer\PHPMailer\PHPMailer $mail){
            $base_url = Arbeitszeit::get_app_ini()["general"]["base_url"];
            $conn = Arbeitszeit::get_conn();
            $sql = "SELECT * FROM `users` WHERE username = '{$username}';";
            $res = mysqli_query($conn, $sql);
            $count = mysqli_num_rows($res);

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
            $token = base64_encode(implode(";", ["username" => $data["username"], "email" => $data["email"]]));
            $email_urlencoded = urlencode($data["email"]);
            $from = "Password Reset Service (AZES)";
            $subject = "Deine Anfrage zum zurücksetzen deines Passworts (AZES)";
            $text = <<< DATA
            
            Hallo {$data["name"]},

            wir haben deine Anfrage, zum Zurücksetzen deines Passwortes erhalten.
            <p>Unten findest du den Link um dein Passwort zurückzusetzen.</p>

            <p>Solltest du das nicht veranlasst haben, informiere deinen Vorgesetzeten sofort oder schicke eine EMail an <a href="mailto:support@openducks.org">support@openducks.org</a></p>

            Link: <a href="http://{$base_url}/suite/actions/auth/reset.php?token={$token}">https://{$base_url}/suite/auth/reset.php?token={$token}</a>

            <p><b>Hinweis: Aus sicherheitstechnischen Gründen ist der Link nur 10 Minuten gültig. Nach Ablauf musst du dein Passwort erneut zurücksetzen.</b></p>

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
            Exceptions::error_rep("An email has been sent to user '$username'. Trigger: Password Reset Request");
            return 1;
        }
    }
}




?>