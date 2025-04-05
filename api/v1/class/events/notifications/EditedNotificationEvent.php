<?php
namespace Arbeitszeit\Events;

use Symfony\Contracts\EventDispatcher\Event;

class EditedNotificationEvent extends Event
{
    public const NAME = 'notifications.edited';

    private string $username;
    private int $id;

    public function __construct(string $username, int $id)
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
