<?php
namespace Arbeitszeit\Events;

use Symfony\Contracts\EventDispatcher\Event;

class DeletedObsoleteNotificationsEvent extends Event
{
    public const NAME = 'notifications.obsolete_deleted';

    private string $username;
    private int $type;

    public function __construct()
    {
        $this->username = 'system';
        $this->type = 0; // Assuming 0 is the type for system events
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getType(): int
    {
        return $this->type;
    }
}
