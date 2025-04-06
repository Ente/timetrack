<?php
namespace Arbeitszeit\Events;

use Symfony\Contracts\EventDispatcher\Event;

class ValidatedLoginEvent extends Event
{
    public const NAME = 'auth.validatedlogin';

    private string $username;
    private string $type; // either "success" or "failed"

    public function __construct(string $username, string $type)
    {
        $this->username = $username;
        $this->type = $type;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
