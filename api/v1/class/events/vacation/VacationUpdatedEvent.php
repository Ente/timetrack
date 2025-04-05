<?php
namespace Arbeitszeit\Events;

use Symfony\Contracts\EventDispatcher\Event;

class VacationUpdatedEvent extends Event
{
    public const NAME = 'vacation.updated';

    private string $username;
    private int $id;
    private string $status;

    public function __construct(string $username, int $id, string $status){
        $this->username = $username;
        $this->id = $id;
        $this->status = $status;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}
