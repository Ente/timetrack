<?php

namespace Arbeitszeit\Mails\Templates;

use Arbeitszeit\Mails\MailsTemplateInterface;
use Arbeitszeit\Arbeitszeit;
use Arbeitszeit\Mails\MailTemplateData;

class DeleteUserTemplate implements MailsTemplateInterface {
    public function render(array $data): MailTemplateData {
        $arbeit = new Arbeitszeit;
        $loc = $arbeit->i18n()->loadLanguage(null, "emails/delete_user");
        $base_url = $arbeit->get_app_ini()["general"]["base_url"];
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

        return new MailTemplateData($loc["subject"], $text, $data["username"]);
    }
}
