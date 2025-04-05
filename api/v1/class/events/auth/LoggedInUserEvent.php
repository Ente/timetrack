<?php
namespace Arbeitszeit\Events;

use Symfony\Contracts\EventDispatcher\Event;

class LoggedInUserEvent extends Event
{
    public const NAME = 'auth.loggedin';

    private string $username;

    /**
     * Success of login, e.g. `success` or `failed`
     * @var string
     */
    private string $type;

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
