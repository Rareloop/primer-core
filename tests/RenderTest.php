<?php

namespace Rareloop\Primer\Tests;

use Rareloop\Primer\Primer;
use Rareloop\Primer\TemplateEngine\Handlebars\Template as HandlebarsTemplateEngine;

class RenderTest extends \PHPUnit_Framework_TestCase
{
    protected $primer;

    /**
     * Bootstrap the system
     */
    public function setup()
    {
        $this->primer = Primer::start(array(
            'basePath' => __DIR__.'/primer-test',
            'templateClass' => HandlebarsTemplateEngine::class,
        ));
    }

    public function testRenderWithChrome()
    {
        $output = $this->primer->getPatterns(array('components/render/test'), true);

        $this->assertEquals("template.hbs\npattern.hbs\ntest/template.hbs", $output);
    }

    public function testRenderWithoutChrome()
    {
        $output = $this->primer->getPatterns(array('components/render/test'), false);

        $this->assertEquals("template.hbs\ntest/template.hbs", $output);
    }

    public function testOverwritingViewFromTemplateJson()
    {
        $output = $this->primer->getTemplate('overwrite-view');

        $this->assertEquals("template-overwrite.hbs\noverwrite-view/template.hbs", $output);
    }

    public function testNotWrappingTemplateWithView()
    {
        $output = $this->primer->getTemplate('not-wrapped');

        $this->assertEquals("not-wrapped/template.hbs", $output);
    }

    public function testPrimerVariablesAreSetWhenWrappedWithView()
    {
        $output = $this->primer->getTemplate('variable-test-wrap');

        $this->assertEquals("bodyClass:true\ntemplate:true\nitems:true\n", $output);
    }

    public function testPrimerVariablesAreSetWhenNotWrappedWithView()
    {
        $output = $this->primer->getTemplate('variable-test-no-wrap');

        $this->assertEquals("bodyClass:true\ntemplate:true", $output);
    }
}
