<?php
namespace Arbeitszeit\Events;

use Symfony\Contracts\EventDispatcher\Event;

class EasymodeWorktimeAddedEvent extends Event
{
    public const NAME = 'worktimes.easymode_started';

    private string $username;
    public function __construct(string $username){
        $this->username = $username;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

}
