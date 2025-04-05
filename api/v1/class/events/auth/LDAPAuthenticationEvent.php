<?php
namespace Arbeitszeit\Events;

use Symfony\Contracts\EventDispatcher\Event;

class LDAPAuthenticationEvent extends Event
{
    public const NAME = 'auth.ldap.sucess';

    private string $username;

    public function __construct(string $username, string $email, string $type)
    {
        $this->username = $username;
    }

    public function getUsername(): string
    {
        return $this->username;
    }
}
