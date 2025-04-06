<?php
namespace Arbeitszeit\Events;

use Symfony\Contracts\EventDispatcher\Event;

class generatedExportEvent extends Event
{
    public const NAME = 'exports.generated';

    private string $username;
    private string $module;
    private string $filename;

    public function __construct(string $username, string $module)
    {
        $this->username = $username;
        $this->module = $module;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getModule(): string
    {
        return $this->module;
    }
}
