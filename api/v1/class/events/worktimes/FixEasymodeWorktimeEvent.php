<?php
namespace Arbeitszeit\Events;

use Symfony\Contracts\EventDispatcher\Event;

class FixEasymodeWorktimeEvent extends Event
{
    public const NAME = 'worktimes.fixeasymode';

    private string $username;

    public function __construct(string $username){
        $this->username = $username;
    }

    public function getUsername(): string
    {
        return $this->username;
    }
}
