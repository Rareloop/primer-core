<?php

namespace Rareloop\Primer\Templating;

use Rareloop\Primer\Events\Event;
use Rareloop\Primer\Events\EventInstanceGroup;
use Exception;

class ViewData extends \stdClass
{
    public function __construct(array $data = array())
    {
        $this->setDataFromArray($data);
        // foreach ($data as $k => $v) {
        //     $this->{$k} = $v;
        // }
    }

    public function toArray()
    {
        $arr = array();

        foreach ($this as $k => $v) {
            $arr[$k] = $v;
        }

        // Force all child objects to be converted to array's too
        return json_decode(json_encode($arr), true);
    }

    public function merge($data)
    {
        $arr = [];

        if (get_class($data) == ViewData::class) {
            $arr = $data->toArray();
        } elseif (is_array($data)) {
            $arr = $data;
        } else {
            throw new Exception('Unexpected data type passed to ViewData merge function');
        }

        $this->setDataFromArray($data->toArray());
    }

    protected function setDataFromArray(array $data)
    {
        foreach ($data as $k => $v) {
            // Use json encode/decode to convert to nested assoc arrays to objects
            $this->{$k} = json_decode(json_encode($v));
        }
    }

    public static function composer($ids, $callable)
    {
        if (!is_array(($ids))) {
            $ids = array($ids);
        }

        $events = [];

        foreach ($ids as $id) {
            $events[] = Event::listen("data.$id", $callable);
        }

        return new EventInstanceGroup($events);
    }

    public static function fire($id, &$data)
    {
        Event::fire('data.' . $id, $data, $id);

        // Fire off events that let us listen for parent paths as well as the main template
        // e.g. components/misc/* and components/*
        $parts = explode('/', $id);

        // We don't need to do the full path as we've already done it
        array_pop($parts);
        $eventString = '';

        foreach ($parts as $part) {
            $eventString .= '/' . $part;
            $eventString = trim($eventString, '/');

            Event::fire('data.' . $eventString . '/*', $data, $id);
        }
    }
}
