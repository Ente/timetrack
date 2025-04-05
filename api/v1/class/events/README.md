# Events

You can use events to trigger certain stuff after the event has been done, like sending custom mails or processing some data.
To register events or listen to them, see the example below:
## Register listener (for any event)

Add somewhere in your plugin:

```php

use Arbeitszeit\Events\EventDispatcherService;
use Arbeitszeit\Events\UserCreatedEvent; // or any other you want to listen to
// make sure your file also has the arbeit.inc.php loaded in

EventDispatcherService::get()->addListener(UserCreatedEvent::class, function (UserCreatedEvent $event){
    // do something
    $user = $event->getUsername();
})
```

## Register own event

Create event class:

```php

namespace Yourplugin\plugin;
use Symfony\Contracts\EventDispatcher\Event;

class YourEvent extends Event
{
    public const NAME = 'yourplugin.your_event';

    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }
}

```

Inside your Main.php / plugin main file:

```php
// require_once "path/to/event.php";
// use Yourplugin\plugin\YourEvent;


... onLoad(): void { // which is called when the plugin is loaded, place below line somewhere else if you want to trigger it later
    EventDispatcherService::get()->dispatch(new YourEvent($data), YourEvent::NAME);
}

```
