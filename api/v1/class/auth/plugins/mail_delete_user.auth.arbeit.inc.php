<?php

namespace Arbeitszeit{
    class MailDeleteUser extends Auth{
        public static function mail_delete_user($user, \PHPMailer\PHPMailer\PHPMailer $mail){
            #$from = "Password Reset Service (AZES)";
            $subject = "Dein Account wurde gelöscht! (AZES)";
            $text = <<< DATA
            <p>Hallo,</p>

            <p>Dein Account wurde von einem Administrator deaktiviert.
            <br>
            <p>Mit freundlichen Grüßen</p>
            <br>
            <b>(automatischer Absender)</b>

            <hr>

            <i>Sie erhalten die Email, da Sie den Bestimmungen zugestimmt hatten. Ihre Email-Adresse, Anmeldename und Password wurde aus dem System entfernt.
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
            Exceptions::error_rep("An email has been sent to user '$user'. Trigger: User deleted.");
            return 1;
        }
    }
}




?>