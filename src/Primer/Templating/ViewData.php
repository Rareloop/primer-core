<?php namespace Rareloop\Primer\Templating;
use Rareloop\Primer\Events\Event;

class ViewData extends \stdClass
{
    public function __construct(array $data)
    {
    	//$data = json_decode(json_encode($data), true);

        foreach ($data as $k => $v) {
            $this->{$k} = $v;
        }
    }

    public function toArray()
    {
        $arr = array();

        foreach($this as $k => $v) {
            $arr[$k] = $v;
        }

        return $arr;
    }

    public static function composer($ids, $callable)
    {
        if (!is_array(($ids))) {
            $ids = array($ids);
        }

        foreach ($ids as $id) {
            Event::listen("data.$id", $callable);
        }
    }
}
