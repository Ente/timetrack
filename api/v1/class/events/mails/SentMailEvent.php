<?php
namespace Arbeitszeit\Events;

use Symfony\Contracts\EventDispatcher\Event;

class SentMailEvent extends Event
{
    public const NAME = 'mail.sent';

    private string $username;
    private string $email;

    private string $type;

    public function __construct(string $username, string $email, string $type)
    {
        $this->username = $username;
        $this->email = $email;
        $this->type = $type;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
