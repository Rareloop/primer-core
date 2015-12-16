<?php

namespace Rareloop\Primer\Tests;

use Rareloop\Primer\Templating\ViewData;
use Rareloop\Primer\Templating\View;
use Rareloop\Primer\TemplateEngine\Handlebars\Template as HandlebarsTemplateEngine;
use Rareloop\Primer\Events\Event;
use Rareloop\Primer\FileSystem;

class EventsTest extends \PHPUnit_Framework_TestCase
{
    protected $primer;

    /**
     * Bootstrap the system
     */
    public function setup()
    {
        $this->primer = \Rareloop\Primer\Primer::start(array(
            'basePath' => __DIR__.'/primer-test',
            'templateClass' => HandlebarsTemplateEngine::class,
        ));
    }

    public function testEventsCanBeBound()
    {
        $testData = new \stdClass;
        $testData->count = 0;

        $event = Event::listen('testEvent', function () use ($testData) {
            $testData->count++;
        });

        Event::fire('testEvent');

        $this->assertEquals(1, $testData->count);
    }

    public function testEventsCanBeUnbound()
    {
        $testData = new \stdClass;
        $testData->count = 0;

        $event = Event::listen('testEvent', function () use ($testData) {
            $testData->count++;
        });

        Event::fire('testEvent');

        $event->stop();
        Event::fire('testEvent');

        $this->assertEquals(1, $testData->count);
    }

    public function testViewDataEventsCanBeUnbound()
    {
        $testData = new \stdClass;
        $testData->count = 0;

        $event = ViewData::composer('components/events/loading-data', function ($data) use ($testData) {
            // Is the data the right type?
            $this->assertEquals(ViewData::class, get_class($data));

            // Increment the count
            $testData->count++;
        });

        FileSystem::getDataForPattern('components/events/loading-data');

        $event->stop();
        FileSystem::getDataForPattern('components/events/loading-data');

        $this->assertEquals(1, $testData->count);
    }

    public function testViewEventsCanBeUnbound()
    {
        $testData = new \stdClass;
        $testData->count = 0;

        $event = View::composer('pattern', function ($data) use ($testData) {
            $testData->count++;
        });

        // Render a pattern with chrome so that it uses the pattern.hbs file
        $this->primer->getPatterns(array('components/events/view-render'), true);

        $event->stop();
        $this->primer->getPatterns(array('components/events/view-render'), true);

        $this->assertEquals(1, $testData->count);
    }

    public function testEventCallbacksRecieveData()
    {
        $testData = new \stdClass;
        $testData->count = 0;

        $eventArgs = 123;

        $event = Event::listen('testEvent', function ($arg) use ($testData) {
            $testData->count++;

            $this->assertEquals(123, $arg);
        });

        Event::fire('testEvent', $eventArgs);

        $this->assertEquals(1, $testData->count);
    }


    public function testLoadingDataShouldTriggerEvent()
    {
        $testData = new \stdClass;
        $testData->count = 0;

        $event = ViewData::composer('components/events/loading-data', function ($data) use ($testData) {
            // Is the data the right type?
            $this->assertEquals(ViewData::class, get_class($data));

            // Increment the count
            $testData->count++;
        });

        $viewData = FileSystem::getDataForPattern('components/events/loading-data');

        $this->assertEquals(1, $testData->count);

        $event->stop();
    }

    public function testLoadingDataShouldTriggerEventsForParentPaths()
    {
        $testData = new \stdClass;
        $testData->count = 0;

        $event1 = ViewData::composer('components/events/*', function ($data) use ($testData) {
            // Is the data the right type?
            $this->assertEquals(ViewData::class, get_class($data));

            // Increment the count
            $testData->count++;
        });

        $event2 = ViewData::composer('components/*', function ($data) use ($testData) {
            // Is the data the right type?
            $this->assertEquals(ViewData::class, get_class($data));

            // Increment the count
            $testData->count++;
        });

        $viewData = FileSystem::getDataForPattern('components/events/loading-data');

        $this->assertEquals(2, $testData->count);

        // Unbind event so it can't mess with other tests
        $event1->stop();
        $event2->stop();
    }

    public function testRenderingAViewShouldTriggerEvent()
    {
        $testData = new \stdClass;
        $testData->count = 0;

        $event = View::composer('pattern', function ($data) use ($testData) {
            // Is the data the right type?
            $this->assertEquals(ViewData::class, get_class($data));

            // Increment the count
            $testData->count++;
        });

        // Render a pattern with chrome so that it uses the pattern.hbs file
        $output = $this->primer->getPatterns(array('components/events/view-render'), true);

        $this->assertEquals(1, $testData->count);

        // Unbind event so it can't mess with other tests
        $event->stop();
    }

    public function testRenderingAPageTemplateShouldTriggerEvent()
    {
        $testData = new \stdClass;
        $testData->count = 0;

        $event = Event::listen('render', function ($data) use ($testData) {
            // Is the data the right type?
            $this->assertEquals(ViewData::class, get_class($data));

            // Increment the count
            $testData->count++;
        });

        $output = $this->primer->getTemplate('render-event');

        $this->assertEquals(1, $testData->count);

        // Unbind event so it can't mess with other tests
        $event->stop();
    }

    public function testRenderEventContainsPrimerData()
    {
        $testData = new \stdClass;
        $testData->count = 0;

        $event = Event::listen('render', function ($data) use ($testData) {
            // Is the data the right type?
            $this->assertEquals(ViewData::class, get_class($data));

            $array = $data->toArray();

            $this->assertArrayHasKey('primer', $array);
            $this->assertArrayHasKey('bodyClass', $array['primer']);
            $this->assertArrayHasKey('template', $array['primer']);
            $this->assertArrayHasKey('items', $array['primer']);

            // Increment the count
            $testData->count++;
        });

        $output = $this->primer->getTemplate('render-event');

        $this->assertEquals(1, $testData->count);

        $event->stop();
    }
}
