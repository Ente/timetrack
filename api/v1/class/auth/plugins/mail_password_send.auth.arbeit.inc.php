<?php

namespace Arbeitszeit{
    class MailPasswordSend extends Auth{
        public static function mail_password_send($username, $type = 1, \PHPMailer\PHPMailer\PHPMailer $mail, $password = null){
            $i18n = new i18n;
            $loc = $i18n->loadLanguage(null, "emails/password_send");
            $base_url = Arbeitszeit::get_app_ini()["general"]["base_url"];
            $conn = new DB;
            $sql = "SELECT * FROM `users` WHERE username = '{$username}';";
            $res = $conn->sendQuery($sql);
            $res->execute([$username]);
            $count = $res->rowCount();
            $ii = Arbeitszeit::get_app_ini()["general"]["app_name"];

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
            $token = rand(111111, PHP_INT_MAX); # token just to identify the user
            $email_urlencoded = urlencode($data["email"]);
            #$from = "Password Reset Service (AZES)";
            $subject = "{$loc["subject"]} | {$ii}";
            if($type ==1){
                $password = "{$loc["note"]}";
            }
            $text = <<< DATA
            <p>{$loc["greetings"]} {$data["name"]},</p>

            <p>{$loc["message"]}: https://{$base_url}/</p>

            <p>{$loc["password"]}: "{$password}"</p>
            
            <br>
            <p>{$loc["end"]}</p>
            <br>
            <b>({$loc["noreply"]})</b>

            <hr>

            <i>{$loc["gdpr"]}: https://{$base_url}/privacy_policy</i>
            <br>
            <span style="color:red"><b>{$loc["confidential"]}</b></span>

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