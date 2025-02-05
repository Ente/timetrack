<?php
namespace Arbeitszeit\Mails\Provider;

use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Exceptions;
use Arbeitszeit\Mails\MailsProviderInterface;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
class PHPMailerMailsProvider implements MailsProviderInterface {

    private static $arbeit;
    private $ini;

    public function __construct(Arbeitszeit $arbeit, $user, $html = false){
        self::$arbeit = $arbeit;
        self::mail_init($user, $html);
    }

    public static function mail_init($user, $html){
        $mail = new PHPMailer(true);
        $ini = self::$arbeit->get_app_ini()["smtp"];
        try {
            $userdata = self::$arbeit->benutzer()->get_user($user);
            $mail->isSMTP();
            $mail->Host = $ini["host"];
            $mail->SMTPAuth = true;
            $mail->Username = $ini["username"];
            $mail->Password = $ini["password"];
            if($ini["usessl"] == true){
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } else {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            }
            $mail->Port = $ini["port"];
            $mail->setFrom($ini["username"], "TimeTrack");

            $r = $mail->addAddress($userdata["email"], $userdata["name"]);
            if(!$r){
                Exceptions::error_rep("Failed to add recipient to mail: " . $mail->ErrorInfo);
                return false;
            }

            if($html == true){
                $mail->isHTML(true);
            }
        } catch(Exception $e){
            Exceptions::error_rep("Failed to initialize mail: " . $e->getMessage());
            Exceptions::error_rep("Error while initiating mails object! | Email:" . $userdata["email"] . " - Username: " . $userdata["username"]);
            return false;
        }

        return $mail;
    }

    public function send(array $mailData){
        $mail = self::mail_init($mailData["username"], true);
        $mail->CharSet = "UTF-8";
        $mail->Encoding = "base64";
        $mail->Subject = $mailData["subject"];
        $mail->Body = $mailData["body"];
        $mail->isHTML(true);
        try {
            $mail->send();
            Exceptions::error_rep("Mail sent to user '{$mailData["username"]}'");
            return true;
        } catch(Exception $e){
            Exceptions::error_rep("Failed to send mail: " . $mail->ErrorInfo);
            return false;
        }
    }
}