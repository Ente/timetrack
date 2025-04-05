<?php
namespace Arbeitszeit\Events;

use Symfony\Contracts\EventDispatcher\Event;

class UserCreatedEvent extends Event
{
    public const NAME = 'user.created';

    private string $username;
    private string $email;

    private int $isAdmin;

    public function __construct(string $username, string $email, int $isAdmin = 0)
    {
        $this->username = $username;
        $this->email = $email;
        $this->isAdmin = $isAdmin;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getIsAdmin(): int
    {
        return $this->isAdmin;
    }
}
