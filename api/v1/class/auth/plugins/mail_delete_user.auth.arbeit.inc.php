<?php

namespace Arbeitszeit{
    class MailDeleteUser extends Auth{
        public static function mail_delete_user($user, \PHPMailer\PHPMailer\PHPMailer $mail){
            $i18n = new i18n;
            $ini = Arbeitszeit::get_app_ini();
            $base_url = $ini["general"]["base_url"];
            $loc = $i18n->loadLanguage(null, "emails/delete_user");
            #$from = "Password Reset Service (AZES)";
            $subject = "{$loc["subject"]} | {$ini["general"]["app_name"]}";
            $text = <<< DATA
            <p>{$loc["greetings"]}</p>

            <p>{$loc["message"]}</p>
            <br>
            <p>{$loc["end"]}</p>
            <br>
            <b>({$loc["noreply"]})</b>

            <hr>

            <i>{$loc["gdpr"]}: https://{$base_url}/privacy_policy</i>

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
            Exceptions::error_rep("An email has been sent to user '$user'. Trigger: User deleted.");
            return 1;
        }
    }
}




?>