<?php

namespace Arbeitszeit\Mails;
interface MailsTemplateInterface {
    public function render(array $data);
}
