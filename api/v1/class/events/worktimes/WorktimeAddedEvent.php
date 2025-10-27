<?php
namespace Arbeitszeit\Events;

use Symfony\Contracts\EventDispatcher\Event;

class WorktimeAddedEvent extends Event
{
    public const NAME = 'worktimes.added';

    private string $username;
    private array $dates;

    private string $Wtype;

    public function __construct(string $username, int $Wtype, array $dates = []){
        $this->username = $username;
        $this->dates = $dates;
        $this->Wtype = $Wtype;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getDates(): array
    {
        return $this->dates;
    }

    public function getWtype(): string
    {
        return $this->Wtype;
    }
}
