<?php

namespace Rareloop\Primer\Events;

class EventInstance implements EventInstanceInterface
{
    protected $closure;
    protected $eventId;

    public function __construct($eventId, $closure)
    {
        $this->eventId = $eventId;
        $this->closure = $closure;
    }

    public function stop()
    {
        Event::eventDispatcherInstance()->removeListener($this->eventId, $this->closure);
    }
}
