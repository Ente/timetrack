<?php
namespace Arbeitszeit\Mails;

class MailTemplateData
{
    private string $subject;
    private string $body;
    private string $username;

    public function __construct(string $subject, string $body, string $username)
    {
        $this->subject = $subject;
        $this->body = $body;
        $this->username = $username;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function toArray(): array
    {
        return [
            "subject" => $this->subject,
            "body" => $this->body,
            "username" => $this->username,
        ];
    }
}
