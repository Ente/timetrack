<?php

namespace Arbeitszeit\Mails\Templates;

use Arbeitszeit\Mails\MailsTemplateInterface;
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Exceptions;
use Arbeitszeit\Mails\MailTemplateData;

class PasswordResetTemplate implements MailsTemplateInterface {
    public function render(array $data): MailTemplateData {
            $arbeit = new Arbeitszeit();
            $i18n = $arbeit->i18n(); 
            $loc = $i18n->loadLanguage(null, "emails/password_reset");
            $base_url = Arbeitszeit::get_app_ini()["general"]["base_url"];
            $ii = Arbeitszeit::get_app_ini()["general"]["app_name"];
            $conn = $arbeit->db();
            $sql = "SELECT * FROM `users` WHERE username = ?;";
            $res = $conn->sendQuery($sql);
            $res->execute([$data["username"]]);
            $count = $res->rowCount();

            if($count == 1){
                $data = $res->fetch(\PDO::FETCH_ASSOC);
            } else {
                Exceptions::error_rep("An error occured while fetching user data from database for user '{$data["username"]}'. See previous message for more information.");
                return [
                    "error" => [
                        "error_code" => 10,
                        "error_message" => "Error while fetching user data (results > 1 or 0)"
                    ]
                ];
            }
            $token = base64_encode(implode(";", ["username" => $data["username"], "email" => $data["email"]]));
            $subject = "{$loc["subject"]} | {$ii}";
            $text = <<< DATA
            
            {$loc["greetings"]} {$data["name"]},

            {$loc["message"]}

            <p>{$loc["note"]}</p>

            Link: <a href="http://{$base_url}/suite/actions/auth/reset.php?token={$token}">https://{$base_url}/suite/auth/reset.php?token={$token}</a>

            <p><b>{$loc["security_note"]}</b></p>

        DATA;
        return new MailTemplateData($subject, $text, $data["username"]);
    }
}
