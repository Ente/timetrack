<?php

namespace Arbeitszeit\Mails\Templates;

use Arbeitszeit\Mails\MailsTemplateInterface;
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Exceptions;
use Arbeitszeit\Mails\MailTemplateData;

class NewUserTemplate implements MailsTemplateInterface {
    public function render(array $data): MailTemplateData {
            $arbeit = new Arbeitszeit();
            $i18n = $arbeit->i18n();
            $loc = $i18n->loadLanguage(null, "emails/new_user");
            $base_url = Arbeitszeit::get_app_ini()["general"]["base_url"];
            $conn = $arbeit->db();
            $sql = "SELECT * FROM `users` WHERE username = ?;";
            $res = $conn->sendQuery($sql);
            $res->execute([$data["username"]]);
            $count = $res->rowCount();
            $ii = Arbeitszeit::get_app_ini()["general"]["app_name"];

            if($count == 1){
                $data = $res->fetch(\PDO::FETCH_ASSOC);
            } else {
                Exceptions::error_rep("An error occured while fetching user data from database for user '{$data["username"]}'. See previous message for more information.");
                return false;
            }
            $subject = "{$loc["subject"]} | {$ii}";
            $text = <<< DATA
            <p>{$loc["greetings"]} {$data["name"]},</p>

            <p>{$loc["message"]}: https://{$base_url}/</p>

            <p>{$loc["username"]}: "{$data["username"]}"</p>
            <p>{$loc["password"]}</p>
            <br>
            <p>{$loc["end"]}</p>
            <br>
            <b>({$loc["noreply"]})</b>

            <hr>

            <i>{$loc["gdpr"]}: https://{$base_url}/privacy_policy </i>

        DATA;
        return new MailTemplateData($subject, $text, $data["username"]);
    }
}
