<?php
namespace Arbeitszeit\Events;

use Symfony\Contracts\EventDispatcher\Event;

class CreatedNotificationEvent extends Event
{
    public const NAME = 'notifications.created';

    private string $username;
    private int $title;

    private string $date;
    private int $id;

    public function __construct(string $username, string $title, string $date, int $id)
    {
        $this->username = $username;
        $this->date = $date;
        $this->id = $id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getTitle(): int
    {
        return $this->title;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
