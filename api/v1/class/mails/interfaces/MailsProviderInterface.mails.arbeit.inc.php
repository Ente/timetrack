<?php

namespace Arbeitszeit\Mails;
interface MailsProviderInterface {
    public function send(array $mailData);
}
