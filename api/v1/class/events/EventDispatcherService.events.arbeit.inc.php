<?php
namespace Arbeitszeit\Events;

use Symfony\Component\EventDispatcher\EventDispatcher;

class EventDispatcherService {
    private static ?EventDispatcher $dispatcher = null;

    public static function get(): EventDispatcher {
        if (!self::$dispatcher) {
            self::$dispatcher = new EventDispatcher();
        }
        return self::$dispatcher;
    }
}
