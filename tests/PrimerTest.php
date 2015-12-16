<?php

namespace Rareloop\Primer\Tests;

use Rareloop\Primer\Primer;
use Rareloop\Primer\TemplateEngine\Handlebars\Template as HandlebarsTemplateEngine;

class PrimerTest extends \PHPUnit_Framework_TestCase
{
    protected $primer;

    public function testCustomPatternPath()
    {
        $primer = Primer::start(array(
            'basePath' => __DIR__.'/primer-test',
            'patternPath' => __DIR__.'/primer-test/patterns-non-standard',
            'templateClass' => HandlebarsTemplateEngine::class,
        ));

        $output = $primer->getPatterns(['components/misc/test'], false);
        $this->assertEquals("template.hbs\ntest/template.hbs", $output);
    }

    public function testCustomViewPath()
    {
        $primer = Primer::start(array(
            'basePath' => __DIR__.'/primer-test',
            'viewPath' => __DIR__.'/primer-test/views-non-standard',
            'templateClass' => HandlebarsTemplateEngine::class,
        ));

        $output = $primer->getPatterns(['components/render/test'], false);
        $this->assertEquals("non-standard/template.hbs\ntest/template.hbs", $output);
    }
}
