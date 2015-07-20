<?php namespace Rareloop\Primer\Events;

class EventObject extends \Symfony\Component\EventDispatcher\Event
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
