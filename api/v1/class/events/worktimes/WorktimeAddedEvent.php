<?php
namespace Arbeitszeit\Events;

use Symfony\Contracts\EventDispatcher\Event;

class WorktimeAddedEvent extends Event
{
    public const NAME = 'worktimes.added';

    private string $username;
    private array $dates;

    public function __construct(string $username, array $dates = []){
        $this->username = $username;
        $this->dates = $dates;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getDates(): array
    {
        return $this->dates;
    }
}
