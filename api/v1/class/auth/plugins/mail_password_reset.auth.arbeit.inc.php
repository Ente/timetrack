<?php

namespace Arbeitszeit{
    class MailPasswordReset extends Auth{
        public static function mail_password_reset($username, \PHPMailer\PHPMailer\PHPMailer $mail){
            $i18n = new i18n; 
            $loc = $i18n->loadLanguage(null, "emails/password_reset");
            $base_url = Arbeitszeit::get_app_ini()["general"]["base_url"];
            $ii = Arbeitszeit::get_app_ini()["general"]["app_name"];
            $conn = new DB;
            $sql = "SELECT * FROM `users` WHERE username = ?;";
            $res = $conn->sendQuery($sql);
            $res->execute([$username]);
            $count = $res->rowCount();

            if($count == 1){
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
            $token = base64_encode(implode(";", ["username" => $data["username"], "email" => $data["email"]]));
            $email_urlencoded = urlencode($data["email"]);
            $from = "Password Reset Service (AZES)";
            $subject = "{$loc["subject"]} | {$ii}";
            $text = <<< DATA
            
            {$loc["greetings"]} {$data["name"]},

            {$loc["message"]}

            <p>{$loc["note"]}</p>

            Link: <a href="http://{$base_url}/suite/actions/auth/reset.php?token={$token}">https://{$base_url}/suite/auth/reset.php?token={$token}</a>

            <p><b>{$loc["security_note"]}</b></p>

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