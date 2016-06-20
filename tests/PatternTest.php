<?php

namespace Rareloop\Primer\Tests;

use Rareloop\Primer\Primer;
use Rareloop\Primer\Renderable\Pattern;
use Rareloop\Primer\TemplateEngine\Handlebars\Template as HandlebarsTemplateEngine;

class PatternTest extends \PHPUnit_Framework_TestCase
{
    protected $primer;

    public function testCustomPatternData()
    {
        $primer = Primer::start(array(
            'basePath' => __DIR__.'/primer-test',
            'templateClass' => HandlebarsTemplateEngine::class,
        ));

        $pattern = new Pattern('components/patterns/custom-data', [
            'name' => 'Test name',
        ]);

        $output = $pattern->render(false);
        $this->assertEquals("Test name", $output);
    }
}
