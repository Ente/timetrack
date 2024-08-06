<?php

namespace Arbeitszeit {
    class MailNewVersion extends Auth
    {
        public static function mail_new_version(\PHPMailer\PHPMailer\PHPMailer $mail)
        {
            $base_url = Arbeitszeit::get_app_ini()["general"]["base_url"];
            $conn = Arbeitszeit::get_conn();
            $sql = "SELECT * FROM `users`;";
            $res = mysqli_query($conn, $sql);
            $count = mysqli_num_rows($res);
            $ii = Arbeitszeit::get_app_ini()["general"]["app_name"];

            if ($count >= 1) {

                foreach (mysqli_fetch_assoc($res) as $user) {

                    $token = rand(111111, PHP_INT_MAX); # token just to identify the user
                    $email_urlencoded = urlencode($user["email"]);
                    #$from = "Password Reset Service (AZES)";
                    $subject = "Du wurdest von {$ii} eingeladen, deine Arbeitszeiten zu erfassen! (AZES)";
                    $text = <<<DATA
            <p>Hallo {$user["name"]},</p>

            <p>wir möchten dich über die aktualisierte Version x.x.x informieren. Unten haben wir dir eine Liste mit den Änderungen gestellt:</p>

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
                    return 1;
                }
            } else {
                Exceptions::error_rep("An error occured while fetching user data from database for user '' | SQL-Error: " . mysqli_error($conn));
                return [
                    "error" => [
                        "error_code" => 10,
                        "error_message" => "Error while fetching user data (results > 1 or 0)"
                    ]
                ];
            }
        }
    }
}




?>