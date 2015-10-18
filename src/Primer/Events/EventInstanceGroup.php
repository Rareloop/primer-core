<?php

namespace Rareloop\Primer\Events;

class EventInstanceGroup implements EventInstanceInterface
{
    protected $events;

    public function __construct(array $events)
    {
        $this->events = $events;
    }

    public function stop()
    {
        foreach ($this->events as $event) {
            $event->stop();
        }
    }
}
