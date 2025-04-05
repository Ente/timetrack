<?php
namespace Arbeitszeit\Events;

use Symfony\Contracts\EventDispatcher\Event;

class VacationCreatedEvent extends Event
{
    public const NAME = 'vacation.created';

    private string $username;
    private string $start;
    private string $end;

    public function __construct(string $username, string $start, string $end)
    {
        $this->username = $username;
        $this->start = $start;
        $this->end = $end;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getStart(): string
    {
        return $this->start;
    }
    public function getEnd(): string
    {
        return $this->end;
    }
}
