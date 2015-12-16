<?php

namespace Rareloop\Primer\Events;

/**
 * Represents a group of EventInstance objects, used to unbind all events in one go.
 * Especially useful for events when also registering parent events too (e.g. components/misc/*)
 */
class EventInstanceGroup implements EventInstanceInterface
{
    protected $events;

    /**
     * Constructor
     *
     * @param array $events An array of EventInstance objects
     */
    public function __construct(array $events)
    {
        $this->events = $events;
    }

    /**
     * Unbind all events in $this->events
     */
    public function stop()
    {
        foreach ($this->events as $event) {
            $event->stop();
        }
    }
}
