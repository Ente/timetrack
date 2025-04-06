<?php
namespace Arbeitszeit\Events;

use Symfony\Contracts\EventDispatcher\Event;

class VacationDeletedEvent extends Event
{
    public const NAME = 'vacation.deleted';

    private string $username;
    private int $id;

    public function __construct(string $username, $id)
    {
        $this->username = $username;
        $this->id = $id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
