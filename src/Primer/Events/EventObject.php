<?php

namespace Rareloop\Primer\Events;

use Symfony\Component\EventDispatcher\Event as SymfonyEvent;

class EventObject extends SymfonyEvent
{
    protected $data;
    protected $eventId;

    public function __construct($eventId, $data)
    {
        $this->setData($data);
        $this->setEventId($eventId);
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setEventId($eventId)
    {
        $this->eventId = $eventId;
    }

    public function getEventId()
    {
        return $this->eventId;
    }
}
