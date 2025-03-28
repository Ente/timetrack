<?php
namespace Arbeitszeit\Mails\Provider;
class DefaultMailsProvider implements \Arbeitszeit\Mails\MailsProviderInterface {
    public function send(array $mailData) {
        $to = $mailData['to'];
        $subject = $mailData['subject'];
        $message = $mailData['body'];
        $headers = $mailData['headers'];
        return mail($to, $subject, $message, $headers);
    }
}