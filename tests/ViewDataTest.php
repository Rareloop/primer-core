<?php

namespace Rareloop\Primer\Tests;

use Rareloop\Primer\Templating\ViewData;

class ViewDataTest extends \PHPUnit_Framework_TestCase
{
    protected $primer;

    public function testToArrayRecursiveObject()
    {
        $data = new ViewData([]);

        $child = new \stdClass;
        $child->key = 'value';

        $data->child = $child;

        $array = $data->toArray();

        $this->assertArrayHasKey('child', $array);
        $this->assertArrayHasKey('key', $array['child']);
    }
}
