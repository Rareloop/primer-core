<?php

namespace Rareloop\Primer\Test\Twig;

use Mockery;
use PHPUnit\Framework\TestCase;
use Rareloop\Primer\Document;
use Rareloop\Primer\Menu;
use Rareloop\Primer\Pattern;
use Rareloop\Primer\Tree;
use Rareloop\Primer\Twig\TwigTemplateRenderer;
use Twig\Environment;

class TwigTemplateRendererTest extends TestCase
{
    use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /** @test */
    public function can_render_a_pattern_without_chrome_with_custom_data()
    {
        $pattern = new Pattern('components/misc/header', ['foo' => 'bar'], '');

        $twig = Mockery::mock(Environment::class);
        $twig->shouldReceive('render')->with('primer-template.twig', [
            'patterns' => [
                $pattern->toArray(),
            ],
            'primer' => [
                'custom' => 'data',
            ]
        ])->andReturn('<p>Testing123</p>');
        $twigRenderer = new TwigTemplateRenderer($twig);

        $this->assertSame('<p>Testing123</p>', $twigRenderer->renderPatternWithoutChrome($pattern, ['custom' => 'data']));
    }

    /** @test */
    public function can_render_a_template_with_custom_data()
    {
        $pattern = new Pattern('components/misc/header', ['foo' => 'bar'], '');

        $twig = Mockery::mock(Environment::class);
        $twig->shouldReceive('render')->with('components/misc/header', [
            'patterns' => [
                $pattern->toArray(),
            ],
            'primer' => [
                'custom' => 'data',
            ]
        ])->andReturn('<p>Testing123</p>');
        $twigRenderer = new TwigTemplateRenderer($twig);

        $this->assertSame('<p>Testing123</p>', $twigRenderer->renderTemplate($pattern, ['custom' => 'data']));
    }

    /** @test */
    public function can_render_an_array_of_patterns()
    {
        $pattern1 = new Pattern('components/misc/header', ['foo' => 'bar'], '');
        $pattern2 = new Pattern('components/misc/footer', ['foo1' => 'bar1'], '');

        $patternsTree = new Tree([
            'components/misc/header',
            'components/misc/footer',
        ]);

        $templatesTree = new Tree([
            'templates/home',
            'templates/contact',
        ]);

        $menu = new Menu;
        $menu
            ->addSection('patterns', $patternsTree)
            ->addSection('templates', $templatesTree);

        $twig = Mockery::mock(Environment::class);
        $twig->shouldReceive('render')->with('primer-template.twig', [
            'menu' => $menu->toArray(),
            'patterns' => [
                $pattern1->toArray(),
                $pattern2->toArray(),
            ],
            'primer' => [],
        ])->andReturn('<p>Testing123</p>');

        $twigRenderer = new TwigTemplateRenderer($twig);

        $this->assertSame('<p>Testing123</p>', $twigRenderer->renderPatterns([$pattern1, $pattern2], $menu));
    }

    /** @test */
    public function can_render_a_document()
    {
        $menu = new Menu;
        $menu->addSection('test', new Tree([]));

        $twig = Mockery::mock(Environment::class);
        $twig->shouldReceive('render')->withArgs(function (string $filename, array $data) {
            $this->assertSame('frontend/overview', $data['document']['id']);
            $this->assertSame('<p>Document</p>', $data['document']['content']);
            $this->assertSame([], $data['menu']['test']['nodes']);

            return $filename === 'primer-template.twig';
        })->andReturn('<body><p>Document</p></body>');

        $twigRenderer = new TwigTemplateRenderer($twig);

        $this->assertSame('<body><p>Document</p></body>', $twigRenderer->renderDocument(new Document('frontend/overview', '<p>Document</p>'), $menu));
    }
}
