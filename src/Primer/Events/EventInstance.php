<?php

namespace Rareloop\Primer\Events;

/**
 * Represents a single instance of a bound event using Event::listen(), this object
 * keeps track of the closure used and the event ID so that it can be unbound.
 */
class EventInstance implements EventInstanceInterface
{
    /**
     * @var Callable
     */
    protected $closure;

    /**
     * @var String
     */
    protected $eventId;

    /**
     * Constructor
     *
     * @param String $eventId
     * @param Callable $closure
     */
    public function __construct($eventId, $closure)
    {
        $this->eventId = $eventId;
        $this->closure = $closure;
    }

    /**
     * Unbind this event
     */
    public function stop()
    {
        Event::eventDispatcherInstance()->removeListener($this->eventId, $this->closure);
    }
}
