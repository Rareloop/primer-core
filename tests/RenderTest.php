<?php

// Test that chrome/no chrome works
// Test that the right data is passed through in the primer namespace
// Test that wrap/no warp works

// namespace Rareloop\Primer\Tests;

// use Rareloop\Primer\Templating\ViewData;
// use Rareloop\Primer\Templating\View;
// use Rareloop\Primer\TemplateEngine\Handlebars\Template as HandlebarsTemplateEngine;
// use Rareloop\Primer\Events\Events;
// use Rareloop\Primer\FileSystem;

// class EventsTest extends \PHPUnit_Framework_TestCase
// {
//     protected $primer;

//     /**
//      * Bootstrap the system
//      */
//     public function setup()
//     {
//         $this->primer = \Rareloop\Primer\Primer::start(array(
//             'basePath' => __DIR__.'/primer-test',
//             'templateClass' => HandlebarsTemplateEngine::class,
//         ));
//     }

//     public function testLoadingDataShouldTriggerEvent()
//     {
//         $testData = new \stdClass;
//         $testData->count = 0;

//         ViewData::composer('components/events/loading-data', function ($data) use ($testData) {
//             // Is the data the right type?
//             $this->assertEquals(ViewData::class, get_class($data));

//             // Increment the count
//             $testData->count++;
//         });

//         $viewData = FileSystem::getDataForPattern('components/events/loading-data');

//         $this->assertEquals(1, $testData->count);
//     }

//     public function testLoadingDataShouldTriggerEventsForParentPaths()
//     {
//         $testData = new \stdClass;
//         $testData->count = 0;

//         ViewData::composer('components/events/*', function ($data) use ($testData) {
//             // Is the data the right type?
//             $this->assertEquals(ViewData::class, get_class($data));

//             // Increment the count
//             $testData->count++;
//         });

//         ViewData::composer('components/*', function ($data) use ($testData) {
//             // Is the data the right type?
//             $this->assertEquals(ViewData::class, get_class($data));

//             // Increment the count
//             $testData->count++;
//         });

//         $viewData = FileSystem::getDataForPattern('components/events/loading-data');

//         $this->assertEquals(2, $testData->count);
//     }

//     public function testRenderingAViewShouldTriggerEvent()
//     {
//         $testData = new \stdClass;
//         $testData->count = 0;

//         View::composer('pattern', function ($data) use ($testData) {
//             // Is the data the right type?
//             $this->assertEquals(ViewData::class, get_class($data));

//             // Increment the count
//             $testData->count++;
//         });

//         // Render a pattern with chrome so that it uses the pattern.hbs file
//         $output = $this->primer->getPatterns(array('components/events/view-render'), true);

//         $this->assertEquals(1, $testData->count);
//     }

//     public function testRenderingAViewShouldTriggerEvent()
//     {
//         $testData = new \stdClass;
//         $testData->count = 0;

//         View::composer('pattern', function ($data) use ($testData) {
//             // Is the data the right type?
//             $this->assertEquals(ViewData::class, get_class($data));

//             // Increment the count
//             $testData->count++;
//         });

//         // Render a pattern with chrome so that it uses the pattern.hbs file
//         $output = $this->primer->getPatterns(array('components/events/view-render'), true);

//         $this->assertEquals(1, $testData->count);
//     }
// }
