<?php

namespace Rareloop\Primer\Events;

use Symfony\Component\EventDispatcher\Event as SymfonyEvent;

class EventObject extends SymfonyEvent
{
    protected $data;
    protected $eventId;
    protected $originalId;

    public function __construct($eventId, $data, $id)
    {
        $this->setData($data);
        $this->setEventId($eventId);
        $this->setOriginalId($id);
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

    public function setOriginalId($originalId)
    {
        $this->originalId = $originalId;
    }

    public function getOriginalId()
    {
        return $this->originalId;
    }
}
