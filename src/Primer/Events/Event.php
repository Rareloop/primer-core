<?php namespace Rareloop\Primer\Events;

use Symfony\Component\EventDispatcher\EventDispatcher;

class Event
{
    protected static $eventDispatcher;

    protected static function eventDispatcherInstance()
    {
        if (!isset(Event::$eventDispatcher)) {
            Event::$eventDispatcher = new EventDispatcher();
        }

        return Event::$eventDispatcher;
    }

    /**
     * Subscribe to an event
     *
     * @param  String  $eventId  The name of the event to listen for
     * @param  Function  $callable A closure to call when the event is fired
     * @param  integer $priority The priority to give this callback
     */
    public static function listen($eventId, $callable, $priority = 0)
    {
        /**
         * Closure to wrap the Symphony event dispather callbacks to make for a nicer API
         *
         * @param EventObject $eventObject
         */
        $closure = function ($eventObject) use ($callable) {

            // Get any passed in data
            $data = $eventObject->getData();

            // Work out if we should stop event propogation
            $returnValue = $callable($data, $eventObject->getEventId());

            if ($returnValue === false) {
                $eventObject->stopPropagation();
            }
        };

        Event::eventDispatcherInstance()->addListener($eventId, $closure, $priority);
    }

    public static function fire($eventId, &$data = false)
    {
        $event = new EventObject($eventId, $data);

        // Trigger the exact event
        Event::eventDispatcherInstance()->dispatch($eventId, $event);

        // If this is a namespaced event then trigger namespace:* too
        if (strpos($eventId, ".") !== false) {
            $parts = explode(".", $eventId);
            $namespace = array_shift($parts);

            Event::eventDispatcherInstance()->dispatch("$namespace.*", $event);
        }
    }
}
