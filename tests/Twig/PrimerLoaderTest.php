<?php

namespace Rareloop\Primer\Test\Twig;

use Mockery;
use PHPUnit\Framework\TestCase;
use Rareloop\Primer\FileSystemPatternProvider;
use Rareloop\Primer\Twig\PrimerLoader;

class PrimerLoaderTest extends TestCase
{
    use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /** @test */
    public function can_test_that_a_template_exists()
    {
        $dataProvider = Mockery::mock(FileSystemPatternProvider::class);
        $dataProvider->shouldReceive('patternExists')->once()->with('components/misc/header')->andReturn(true);
        $loader = new PrimerLoader($dataProvider);

        $this->assertTrue($loader->exists('components/misc/header'));
    }

    /** @test */
    public function can_test_that_a_template_exists_does_not_exist()
    {
        $dataProvider = Mockery::mock(FileSystemPatternProvider::class);
        $dataProvider->shouldReceive('patternExists')->once()->with('components/misc/header')->andReturn(false);
        $loader = new PrimerLoader($dataProvider);

        $this->assertFalse($loader->exists('components/misc/header'));
    }

    /** @test */
    public function can_get_a_consistent_cache_key()
    {
        $dataProvider = Mockery::mock(FileSystemPatternProvider::class);
        $loader = new PrimerLoader($dataProvider);

        $this->assertSame($loader->getCacheKey('components/misc/header'), $loader->getCacheKey('components/misc/header'));
        $this->assertSame($loader->getCacheKey('components/misc/footer'), $loader->getCacheKey('components/misc/footer'));
        $this->assertNotSame($loader->getCacheKey('components/misc/header'), $loader->getCacheKey('components/misc/footer'));
    }

    /** @test */
    public function can_get_source_context()
    {
        $dataProvider = Mockery::mock(FileSystemPatternProvider::class);
        $dataProvider->shouldReceive('getPatternTemplate')->once()->with('components/misc/header')->andReturn('<h1>Hello World</h1>');
        $loader = new PrimerLoader($dataProvider);

        $source = $loader->getSourceContext('components/misc/header');

        $this->assertInstanceOf(\Twig_Source::class, $source);
        $this->assertSame('<h1>Hello World</h1>', $source->getCode());
        $this->assertSame('components/misc/header', $source->getName());
    }

    /** @test */
    public function isFresh_returns_false_if_cache_last_modified_is_later_than_template_last_modified()
    {
        $dataProvider = Mockery::mock(FileSystemPatternProvider::class);
        $dataProvider->shouldReceive('getPatternTemplateLastModified')->once()->with('components/misc/header')->andReturn(100);
        $loader = new PrimerLoader($dataProvider);

        $this->assertFalse($loader->isFresh('components/misc/header', 200));
    }

    /** @test */
    public function isFresh_returns_false_if_cache_last_modified_is_equal_to_template_last_modified()
    {
        $dataProvider = Mockery::mock(FileSystemPatternProvider::class);
        $dataProvider->shouldReceive('getPatternTemplateLastModified')->once()->with('components/misc/header')->andReturn(100);
        $loader = new PrimerLoader($dataProvider);

        $this->assertFalse($loader->isFresh('components/misc/header', 100));
    }

    /** @test */
    public function isFresh_returns_true_if_cache_last_modified_is_earlier_than_template_last_modified()
    {
        $dataProvider = Mockery::mock(FileSystemPatternProvider::class);
        $dataProvider->shouldReceive('getPatternTemplateLastModified')->once()->with('components/misc/header')->andReturn(200);
        $loader = new PrimerLoader($dataProvider);

        $this->assertTrue($loader->isFresh('components/misc/header', 100));
    }
}
