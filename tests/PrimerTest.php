<?php

namespace Rareloop\Primer\Test;

use Mockery;
use PHPUnit\Framework\TestCase;
use Rareloop\Primer\Contracts\DocumentProvider;
use Rareloop\Primer\Contracts\DocumentRenderer;
use Rareloop\Primer\Contracts\PatternProvider;
use Rareloop\Primer\Contracts\TemplateRenderer;
use Rareloop\Primer\Document;
use Rareloop\Primer\Exceptions\PatternNotFoundException;
use Rareloop\Primer\Menu;
use Rareloop\Primer\Pattern;
use Rareloop\Primer\Primer;
use Rareloop\Primer\Tree;

class PrimerTest extends TestCase
{
    use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /** @test */
    public function can_render_a_pattern_without_chrome()
    {
        $templateRenderer = Mockery::mock(TemplateRenderer::class);
        $templateRenderer
            ->shouldReceive('renderPatternWithoutChrome')
            ->once()
            ->withArgs(function (Pattern $pattern) {
                return $pattern->id() === 'components/misc/header';
            })
            ->andReturn('<p>Testing123</p>');

        $patternProvider = Mockery::mock(PatternProvider::class);
        $patternProvider
            ->shouldReceive('getPattern')->with('components/misc/header', 'default')->andReturn(new Pattern('components/misc/header', [], ''));

        $templateProvider = Mockery::mock(PatternProvider::class);
        $documentProvider = Mockery::mock(DocumentProvider::class);

        $primer = new Primer($templateRenderer, $patternProvider, $templateProvider, $documentProvider);

        $this->assertSame('<p>Testing123</p>', $primer->renderPatternWithoutChrome('components/misc/header'));
    }

    /** @test */
    public function can_render_a_pattern_without_chrome_with_a_custom_state()
    {
        $templateRenderer = Mockery::mock(TemplateRenderer::class);
        $templateRenderer
            ->shouldReceive('renderPatternWithoutChrome')
            ->once()
            ->withArgs(function (Pattern $pattern, array $primerData) {
                $this->assertSame('Header', $primerData['title']);
                $this->assertSame('pattern', $primerData['mode']);

                return $pattern->id() === 'components/misc/header' && $pattern->state() === 'error';
            })
            ->andReturn('<p>Testing123</p>');

        $patternProvider = Mockery::mock(PatternProvider::class);
        $patternProvider
            ->shouldReceive('getPattern')->with('components/misc/header', 'error')->andReturn(new Pattern('components/misc/header', [], '', 'error'));

        $templateProvider = Mockery::mock(PatternProvider::class);
        $documentProvider = Mockery::mock(DocumentProvider::class);

        $primer = new Primer($templateRenderer, $patternProvider, $templateProvider, $documentProvider);

        $this->assertSame('<p>Testing123</p>', $primer->renderPatternWithoutChrome('components/misc/header', 'error'));
    }

    /** @test */
    public function can_render_a_template()
    {
        $templateRenderer = Mockery::mock(TemplateRenderer::class);
        $templateRenderer
            ->shouldReceive('renderTemplate')
            ->once()
            ->withArgs(function (Pattern $pattern, array $primerData) {
                $this->assertSame('Home', $primerData['title']);
                $this->assertSame('template', $primerData['mode']);

                return $pattern->id() === 'templates/home';
            })
            ->andReturn('<p>Testing123</p>');

        $patternProvider = Mockery::mock(PatternProvider::class);

        $templateProvider = Mockery::mock(PatternProvider::class);
        $templateProvider
            ->shouldReceive('getPattern')->with('templates/home', 'default')->andReturn(new Pattern('templates/home', [], ''));

        $documentProvider = Mockery::mock(DocumentProvider::class);

        $primer = new Primer($templateRenderer, $patternProvider, $templateProvider, $documentProvider);

        $this->assertSame('<p>Testing123</p>', $primer->renderTemplate('templates/home'));
    }


    /** @test */
    public function can_get_current_template_default()
    {
        $templateRenderer = Mockery::mock(TemplateRenderer::class);
        $templateRenderer->shouldReceive('renderTemplate')->once();

        $patternProvider = Mockery::mock(PatternProvider::class);

        $templateProvider = Mockery::mock(PatternProvider::class);
        $templateProvider->shouldReceive('getPattern')->once();

        $documentProvider = Mockery::mock(DocumentProvider::class);

        $primer = new Primer($templateRenderer, $patternProvider, $templateProvider, $documentProvider);

        $primer->renderTemplate('templates/home');

        $this->assertSame('templates/home', $primer->currentTemplateId());
    }

    /** @test */
    public function can_get_current_template_with_state()
    {
        $templateRenderer = Mockery::mock(TemplateRenderer::class);
        $templateRenderer->shouldReceive('renderTemplate')->once();

        $patternProvider = Mockery::mock(PatternProvider::class);

        $templateProvider = Mockery::mock(PatternProvider::class);
        $templateProvider->shouldReceive('getPattern')->once();

        $documentProvider = Mockery::mock(DocumentProvider::class);

        $primer = new Primer($templateRenderer, $patternProvider, $templateProvider, $documentProvider);

        $primer->renderTemplate('templates/home', 'error');

        $this->assertSame('templates/home', $primer->currentTemplateId());
    }

    /** @test */
    public function can_get_current_template_state_default()
    {
        $templateRenderer = Mockery::mock(TemplateRenderer::class);
        $templateRenderer->shouldReceive('renderTemplate')->once();

        $patternProvider = Mockery::mock(PatternProvider::class);

        $templateProvider = Mockery::mock(PatternProvider::class);
        $templateProvider->shouldReceive('getPattern')->once();

        $documentProvider = Mockery::mock(DocumentProvider::class);

        $primer = new Primer($templateRenderer, $patternProvider, $templateProvider, $documentProvider);

        $primer->renderTemplate('templates/home');

        $this->assertSame('default', $primer->currentTemplateState());
    }

    /** @test */
    public function can_get_current_template_state()
    {
        $templateRenderer = Mockery::mock(TemplateRenderer::class);
        $templateRenderer->shouldReceive('renderTemplate')->once();

        $patternProvider = Mockery::mock(PatternProvider::class);

        $templateProvider = Mockery::mock(PatternProvider::class);
        $templateProvider->shouldReceive('getPattern')->once();

        $documentProvider = Mockery::mock(DocumentProvider::class);

        $primer = new Primer($templateRenderer, $patternProvider, $templateProvider, $documentProvider);

        $primer->renderTemplate('templates/home', 'error');

        $this->assertSame('error', $primer->currentTemplateState());
    }

    /** @test */
    public function can_render_a_list_of_patterns()
    {
        $patternIds = [
            'components/misc/header',
            'components/misc/footer',
            'components/not-misc/another',
        ];

        $templateIds = [
            'templates/home',
            'templates/contact',
        ];

        $documentIds = [
            'frontend/overview',
            'getting-started',
        ];

        $templateRenderer = Mockery::mock(TemplateRenderer::class);
        $templateRenderer
            ->shouldReceive('renderPatterns')
            ->once()
            ->withArgs(function (array $patterns, Menu $menu, array $primerData) use ($documentIds, $patternIds, $templateIds) {
                $this->assertMenu($menu, 'components/misc', $documentIds, $patternIds, $templateIds);
                $this->assertUIVisible($primerData);
                $this->assertSame('Misc', $primerData['title']);
                $this->assertSame('pattern', $primerData['mode']);

                return
                    count($patterns) === 2 &&
                    $patterns[0]->id() === 'components/misc/header' &&
                    $patterns[1]->id() === 'components/misc/footer';
            })
            ->andReturn('<p>Testing123</p>');

        $patternProvider = Mockery::mock(PatternProvider::class);
        $patternProvider
            ->shouldReceive('allPatternIds')->andReturn($patternIds)
            ->shouldReceive('getPattern')->with('components/misc/header', 'default')->once()->andReturn(new Pattern('components/misc/header', [], ''))
            ->shouldReceive('getPattern')->with('components/misc/footer', 'default')->once()->andReturn(new Pattern('components/misc/footer', [], ''));

        $templateProvider = Mockery::mock(PatternProvider::class);
        $templateProvider->shouldReceive('allPatternIds')->once()->andReturn($templateIds);

        $documentProvider = Mockery::mock(DocumentProvider::class);
        $documentProvider->shouldReceive('allDocumentIds')->once()->andReturn($documentIds);

        $primer = new Primer($templateRenderer, $patternProvider, $templateProvider, $documentProvider);

        $this->assertSame('<p>Testing123</p>', $primer->renderPatterns('components/misc'));
    }

    /** @test */
    public function can_get_current_pattern_ids_when_rendering_all_patterns()
    {
        $patternIds = [
            'components/misc/header',
            'components/misc/footer',
            'components/not-misc/another',
        ];

        $templateRenderer = Mockery::mock(TemplateRenderer::class);
        $templateRenderer->shouldReceive('renderPatterns')->once();

        $patternProvider = Mockery::mock(PatternProvider::class);
        $templateProvider = Mockery::mock(PatternProvider::class);
        $templateProvider->shouldReceive('allPatternIds')->once()->andReturn([]);

        $documentProvider = Mockery::mock(DocumentProvider::class);
        $documentProvider->shouldReceive('allDocumentIds')->once()->andReturn([]);

        $primer = new Primer($templateRenderer, $patternProvider, $templateProvider, $documentProvider);

        $patternProvider->shouldReceive('allPatternIds')->twice()->andReturn($patternIds);

        $patternProvider->shouldReceive('getPattern')->with('components/misc/header', 'default')->once()->andReturnUsing(function () use ($primer) {
            $this->assertSame('components/misc/header', $primer->currentPatternId());

            return new Pattern('components/misc/header', [], '');
        });

        $patternProvider->shouldReceive('getPattern')->with('components/misc/footer', 'default')->once()->andReturnUsing(function () use ($primer) {
            $this->assertSame('components/misc/footer', $primer->currentPatternId());

            return new Pattern('components/misc/footer', [], '');
        });

        $patternProvider->shouldReceive('getPattern')->with('components/not-misc/another', 'default')->once()->andReturnUsing(function () use ($primer) {
            $this->assertSame('components/not-misc/another', $primer->currentPatternId());

            return new Pattern('components/not-misc/another', [], '');
        });

        $primer->renderPatterns('components');
    }

    /** @test */
    public function can_get_current_pattern_states_when_rendering_all_patterns()
    {
        $patternIds = [
            'components/misc/header',
            'components/misc/footer',
            'components/not-misc/another',
        ];

        $templateRenderer = Mockery::mock(TemplateRenderer::class);
        $templateRenderer->shouldReceive('renderPatterns')->once();

        $patternProvider = Mockery::mock(PatternProvider::class);
        $templateProvider = Mockery::mock(PatternProvider::class);
        $templateProvider->shouldReceive('allPatternIds')->once()->andReturn([]);

        $documentProvider = Mockery::mock(DocumentProvider::class);
        $documentProvider->shouldReceive('allDocumentIds')->once()->andReturn([]);

        $primer = new Primer($templateRenderer, $patternProvider, $templateProvider, $documentProvider);

        $patternProvider->shouldReceive('allPatternIds')->twice()->andReturn($patternIds);

        $patternProvider->shouldReceive('getPattern')->with('components/misc/header', 'default')->once()->andReturnUsing(function () use ($primer) {
            $this->assertSame('default', $primer->currentPatternState());

            return new Pattern('components/misc/header', [], '');
        });

        $patternProvider->shouldReceive('getPattern')->with('components/misc/footer', 'default')->once()->andReturnUsing(function () use ($primer) {
            $this->assertSame('default', $primer->currentPatternState());

            return new Pattern('components/misc/footer', [], '');
        });

        $patternProvider->shouldReceive('getPattern')->with('components/not-misc/another', 'default')->once()->andReturnUsing(function () use ($primer) {
            $this->assertSame('default', $primer->currentPatternState());

            return new Pattern('components/not-misc/another', [], '');
        });

        $primer->renderPatterns('components');
    }

    /** @test */
    public function correct_patterns_are_rendered_when_one_name_is_contained_in_another()
    {
        $patternIds = [
            'components/misc/header',
            'components/misc/headers',
        ];

        $templateRenderer = Mockery::mock(TemplateRenderer::class);
        $templateRenderer
            ->shouldReceive('renderPatterns')
            ->once()
            ->withArgs(function (array $patterns, Menu $menu, array $primerData) use ($patternIds) {
                return
                    count($patterns) === 1 &&
                    $patterns[0]->id() === 'components/misc/header';
            })
            ->andReturn('<p>Testing123</p>');

        $patternProvider = Mockery::mock(PatternProvider::class);
        $patternProvider
            ->shouldReceive('allPatternIds')->andReturn($patternIds)
            ->shouldReceive('getPattern')->with('components/misc/header', 'default')->once()->andReturn(new Pattern('components/misc/header', [], ''));

        $templateProvider = Mockery::mock(PatternProvider::class);
        $templateProvider->shouldReceive('allPatternIds')->once()->andReturn([]);

        $documentProvider = Mockery::mock(DocumentProvider::class);
        $documentProvider->shouldReceive('allDocumentIds')->once()->andReturn([]);

        $primer = new Primer($templateRenderer, $patternProvider, $templateProvider, $documentProvider);

        $this->assertSame('<p>Testing123</p>', $primer->renderPatterns('components/misc/header'));
    }

    /** @test */
    public function renderPatterns_throws_exception_if_no_matching_patterns_are_found()
    {
        $this->expectException(\Rareloop\Primer\Exceptions\PatternNotFoundException::class);

        $patternIds = [
            'components/misc/header',
            'components/misc/footer',
            'components/not-misc/another',
        ];

        $templateRenderer = Mockery::mock(TemplateRenderer::class);

        $patternProvider = Mockery::mock(PatternProvider::class);
        $patternProvider->shouldReceive('allPatternIds')->andReturn($patternIds);

        $templateProvider = Mockery::mock(PatternProvider::class);
        $templateProvider->shouldReceive('allPatternIds')->andReturn([]);

        $documentProvider = Mockery::mock(DocumentProvider::class);
        $documentProvider->shouldReceive('allDocumentIds')->andReturn([]);

        $primer = new Primer($templateRenderer, $patternProvider, $templateProvider, $documentProvider);

        $primer->renderPatterns('does/not/exist');
    }

    /** @test */
    public function can_render_a_pattern_with_state()
    {
        $patternIds = [
            'components/misc/header',
            'components/misc/footer',
            'components/not-misc/another',
        ];

        $templateIds = [
            'templates/home',
            'templates/contact',
        ];

        $documentIds = [
            'frontend/overview',
            'getting-started',
        ];

        $templateRenderer = Mockery::mock(TemplateRenderer::class);
        $templateRenderer
            ->shouldReceive('renderPatterns')
            ->once()
            ->withArgs(function (array $patterns, Menu $menu, array $primerData) use ($patternIds, $templateIds, $documentIds) {
                $this->assertMenu($menu, 'components/misc/header', $documentIds, $patternIds, $templateIds);
                $this->assertUIVisible($primerData);
                $this->assertSame('Header', $primerData['title']);

                return
                    count($patterns) === 1 &&
                    $patterns[0]->id() === 'components/misc/header' &&
                    $patterns[0]->state() === 'error';
            })
            ->andReturn('<p>Testing123</p>');

        $patternProvider = Mockery::mock(PatternProvider::class);
        $patternProvider
            ->shouldReceive('allPatternIds')->once()->andReturn($patternIds)
            ->shouldReceive('getPattern')->with('components/misc/header', 'error')->once()->andReturn(new Pattern('components/misc/header', [], '', 'error'));

        $templateProvider = Mockery::mock(PatternProvider::class);
        $templateProvider->shouldReceive('allPatternIds')->once()->andReturn($templateIds);

        $documentProvider = Mockery::mock(DocumentProvider::class);
        $documentProvider->shouldReceive('allDocumentIds')->once()->andReturn($documentIds);

        $primer = new Primer($templateRenderer, $patternProvider, $templateProvider, $documentProvider);

        $this->assertSame('<p>Testing123</p>', $primer->renderPattern('components/misc/header', 'error'));
    }

    /** @test */
    public function can_render_a_document()
    {
        $documentIds = [
            'frontend/overview',
            'getting-started',
        ];

        $patternIds = [
            'components/misc/header',
            'components/misc/footer',
            'components/not-misc/another',
        ];

        $templateIds = [
            'templates/home',
            'templates/contact',
        ];

        $templateRenderer = Mockery::mock(TemplateRenderer::class);
        $templateRenderer
            ->shouldReceive('renderDocument')
            ->once()
            ->withArgs(function (Document $doc, Menu $menu, array $primerData) use ($documentIds, $patternIds, $templateIds) {
                $this->assertMenu($menu, 'frontend/overview', $documentIds, $patternIds, $templateIds);
                $this->assertUIVisible($primerData);
                $this->assertSame('title', $primerData['title']);
                $this->assertSame('description', $primerData['description']);
                $this->assertSame('document', $primerData['mode']);

                return $doc->id() === 'frontend/overview' && $doc->content() === 'Document';
            })
            ->andReturn('<body><p>Document</p></body>');

        $patternProvider = Mockery::mock(PatternProvider::class);
        $patternProvider->shouldReceive('allPatternIds')->once()->andReturn($patternIds);

        $templateProvider = Mockery::mock(PatternProvider::class);
        $templateProvider->shouldReceive('allPatternIds')->once()->andReturn($templateIds);

        $doc = new Document('frontend/overview', 'Document');
        $doc->setTitle('title');
        $doc->setDescription('description');

        $documentProvider = Mockery::mock(DocumentProvider::class);
        $documentProvider
            ->shouldReceive('allDocumentIds')->once()->andReturn($documentIds)
            ->shouldReceive('getDocument')->once()->with('frontend/overview')->andReturn($doc);

        $primer = new Primer($templateRenderer, $patternProvider, $templateProvider, $documentProvider);

        $this->assertSame('<body><p>Document</p></body>', $primer->renderDocument('frontend/overview'));
    }

    /** @test */
    public function can_get_the_menu()
    {
        $templateRenderer = Mockery::mock(TemplateRenderer::class);
        $patternProvider = Mockery::mock(PatternProvider::class);
        $patternProvider->shouldReceive('allPatternIds')->once()->andReturn(['patterns']);
        $templateProvider = Mockery::mock(PatternProvider::class);
        $templateProvider->shouldReceive('allPatternIds')->once()->andReturn(['templates']);
        $documentProvider = Mockery::mock(DocumentProvider::class);
        $documentProvider->shouldReceive('allDocumentIds')->once()->andReturn(['documents']);
        $primer = new Primer($templateRenderer, $patternProvider, $templateProvider, $documentProvider);

        $menu = $primer->getMenu();

        $this->assertInstanceOf(Menu::class, $menu);
        $this->assertTrue($menu->hasSection('patterns'));
        $this->assertTrue($menu->hasSection('templates'));
        $this->assertTrue($menu->hasSection('documents'));

        // Check the id's specified in the mocks above are in the menu
        $this->assertSame('patterns', $menu->getSection('patterns')->toArray()[0]['id']);
        $this->assertSame('templates', $menu->getSection('templates')->toArray()[0]['id']);
        $this->assertSame('documents', $menu->getSection('documents')->toArray()[0]['id']);
    }

    /** @test */
    public function can_get_pattern_state_data()
    {
        $templateRenderer = Mockery::mock(TemplateRenderer::class);
        $patternProvider = Mockery::mock(PatternProvider::class);
        $patternProvider
            ->shouldReceive('getPatternStateData')
            ->once()
            ->with('components/common/header', 'state-name')
            ->andReturn(['foo' => 'bar']);

        $templateProvider = Mockery::mock(PatternProvider::class);
        $documentProvider = Mockery::mock(DocumentProvider::class);

        $primer = new Primer($templateRenderer, $patternProvider, $templateProvider, $documentProvider);

        $data = $primer->getPatternStateData('components/common/header', 'state-name');

        $this->assertSame(['foo' => 'bar'], $data);
    }

    /** @test */
    public function current_pattern_and_state_is_set_for_get_pattern_state_data()
    {
        $templateRenderer = Mockery::mock(TemplateRenderer::class);
        $patternProvider = Mockery::mock(PatternProvider::class);

        $templateProvider = Mockery::mock(PatternProvider::class);
        $documentProvider = Mockery::mock(DocumentProvider::class);

        $primer = new Primer($templateRenderer, $patternProvider, $templateProvider, $documentProvider);

        $patternProvider
            ->shouldReceive('getPatternStateData')
            ->once()
            ->with('components/common/header', 'state-name')
            ->andReturnUsing(function () use ($primer) {
                return ['pattern' => $primer->currentPatternId(), 'state' => $primer->currentPatternState()];
            });

        $data = $primer->getPatternStateData('components/common/header', 'state-name');

        $this->assertSame(['pattern' => 'components/common/header', 'state' => 'state-name'], $data);
    }

    /** @test */
    public function current_pattern_and_state_is_set_for_get_pattern_state_data_when_using_include()
    {
        $templateRenderer = Mockery::mock(TemplateRenderer::class);
        $patternProvider = Mockery::mock(PatternProvider::class);

        $templateProvider = Mockery::mock(PatternProvider::class);
        $documentProvider = Mockery::mock(DocumentProvider::class);

        $primer = new Primer($templateRenderer, $patternProvider, $templateProvider, $documentProvider);

        $patternProvider
            ->shouldReceive('getPatternStateData')
            ->once()
            ->with('components/common/header', 'state-name')
            ->andReturnUsing(function ($id) use ($primer) {
                return [
                    'this' => ['pattern' => $primer->currentPatternId(), 'state' => $primer->currentPatternState()],
                    'sub' => $primer->getPatternStateData('components/common/footer', 'another-state'),
                ];
            });

        $patternProvider
            ->shouldReceive('getPatternStateData')
            ->once()
            ->with('components/common/footer', 'another-state')
            ->andReturnUsing(function ($id) use ($primer) {
                return ['pattern' => $primer->currentPatternId(), 'state' => $primer->currentPatternState()];
            });

        $data = $primer->getPatternStateData('components/common/header', 'state-name');

        $this->assertSame([
            'this' => ['pattern' => 'components/common/header', 'state' => 'state-name'],
            'sub' => ['pattern' => 'components/common/footer', 'state' => 'another-state'],
        ], $data);
    }

    /** @test */
    public function can_get_template_state_data()
    {
        $templateRenderer = Mockery::mock(TemplateRenderer::class);
        $patternProvider = Mockery::mock(PatternProvider::class);

        $templateProvider = Mockery::mock(PatternProvider::class);
        $templateProvider
            ->shouldReceive('getPatternStateData')
            ->once()
            ->with('home', 'state-name')
            ->andReturn(['foo' => 'bar']);
        $documentProvider = Mockery::mock(DocumentProvider::class);

        $primer = new Primer($templateRenderer, $patternProvider, $templateProvider, $documentProvider);

        $data = $primer->getTemplateStateData('home', 'state-name');

        $this->assertSame(['foo' => 'bar'], $data);
    }

    /** @test */
    public function current_template_id_is_set_when_calling_can_get_template_state_data()
    {
        $templateRenderer = Mockery::mock(TemplateRenderer::class);
        $patternProvider = Mockery::mock(PatternProvider::class);

        $templateProvider = Mockery::mock(PatternProvider::class);
        $documentProvider = Mockery::mock(DocumentProvider::class);

        $primer = new Primer($templateRenderer, $patternProvider, $templateProvider, $documentProvider);

        $templateProvider
            ->shouldReceive('getPatternStateData')
            ->once()
            ->with('home', 'state-name')
            ->andReturnUsing(function () use ($primer) {
                return ['template' => $primer->currentTemplateId(), 'state' => $primer->currentTemplateState()];
            });

        $data = $primer->getTemplateStateData('home', 'state-name');

        $this->assertSame(['template' => 'home', 'state' => 'state-name'], $data);
    }

    /** @test */
    public function current_template_id_is_set_when_calling_can_get_template_state_data_when_using_includes()
    {
        $templateRenderer = Mockery::mock(TemplateRenderer::class);
        $patternProvider = Mockery::mock(PatternProvider::class);

        $templateProvider = Mockery::mock(PatternProvider::class);
        $documentProvider = Mockery::mock(DocumentProvider::class);

        $primer = new Primer($templateRenderer, $patternProvider, $templateProvider, $documentProvider);

        $templateProvider
            ->shouldReceive('getPatternStateData')
            ->once()
            ->with('home', 'state-name')
            ->andReturnUsing(function () use ($primer) {
                return [
                    'this' => ['template' => $primer->currentTemplateId(), 'state' => $primer->currentTemplateState()],
                    'sub' => $primer->getTemplateStateData('away', 'another-state'),
                ];
            });

        $templateProvider
            ->shouldReceive('getPatternStateData')
            ->once()
            ->with('away', 'another-state')
            ->andReturnUsing(function () use ($primer) {
                return ['template' => $primer->currentTemplateId(), 'state' => $primer->currentTemplateState()];
            });

        $data = $primer->getTemplateStateData('home', 'state-name');

        $this->assertSame([
            'this' => ['template' => 'home', 'state' => 'state-name'],
            'sub' => ['template' => 'away', 'state' => 'another-state'],
        ], $data);
    }

    /** @test */
    public function getPatternStateData_returns_empty_array_when_pattern_is_not_valid()
    {
        $templateRenderer = Mockery::mock(TemplateRenderer::class);
        $patternProvider = Mockery::mock(PatternProvider::class);
        $patternProvider
            ->shouldReceive('getPatternStateData')
            ->once()
            ->andThrow(new PatternNotFoundException);

        $templateProvider = Mockery::mock(PatternProvider::class);
        $documentProvider = Mockery::mock(DocumentProvider::class);

        $primer = new Primer($templateRenderer, $patternProvider, $templateProvider, $documentProvider);

        $data = $primer->getPatternStateData('components/common/header', 'state-name');

        $this->assertSame([], $data);
    }

    /** @test */
    public function getTemplateStateData_returns_empty_array_when_pattern_is_not_valid()
    {
        $templateRenderer = Mockery::mock(TemplateRenderer::class);
        $patternProvider = Mockery::mock(PatternProvider::class);

        $templateProvider = Mockery::mock(PatternProvider::class);
        $templateProvider
            ->shouldReceive('getPatternStateData')
            ->once()
            ->andThrow(new PatternNotFoundException);
        $documentProvider = Mockery::mock(DocumentProvider::class);

        $primer = new Primer($templateRenderer, $patternProvider, $templateProvider, $documentProvider);

        $data = $primer->getTemplateStateData('home', 'state-name');

        $this->assertSame([], $data);
    }

    /** @test */
    public function custom_set_data_is_applied_by_renderDocument()
    {
        $templateRenderer = Mockery::mock(TemplateRenderer::class);
        $templateRenderer
            ->shouldReceive('renderDocument')
            ->once()
            ->withArgs(function (Document $doc, Menu $menu, array $primerData) {
                $this->assertSame('bar', $primerData['foo']);

                return true;
            })
            ->andReturn('<body><p>Document</p></body>');

        $patternProvider = Mockery::mock(PatternProvider::class);
        $patternProvider->shouldReceive('allPatternIds')->andReturn([]);

        $templateProvider = Mockery::mock(PatternProvider::class);
        $templateProvider->shouldReceive('allPatternIds')->andReturn([]);

        $documentProvider = Mockery::mock(DocumentProvider::class);
        $documentProvider
            ->shouldReceive('allDocumentIds')->once()->andReturn(['frontend/overview'])
            ->shouldReceive('getDocument')->once()->with('frontend/overview')->andReturn(new Document('frontend/overview', 'Document'));

        $primer = new Primer($templateRenderer, $patternProvider, $templateProvider, $documentProvider);
        $primer->setCustomData('foo', 'bar');

        $this->assertSame('<body><p>Document</p></body>', $primer->renderDocument('frontend/overview'));
    }

    /** @test */
    public function custom_set_data_is_applied_by_renderPatterns()
    {
        $templateRenderer = Mockery::mock(TemplateRenderer::class);
        $templateRenderer
            ->shouldReceive('renderPatterns')
            ->once()
            ->withArgs(function (array $patterns, Menu $menu, array $primerData) {
                $this->assertSame('bar', $primerData['foo']);

                return true;
            })
            ->andReturn('<body><p>Document</p></body>');

        $patternProvider = Mockery::mock(PatternProvider::class);
        $patternProvider
            ->shouldReceive('allPatternIds')->andReturn(['frontend/overview'])
            ->shouldReceive('getPattern')->andReturn(Mockery::mock(Pattern::class));

        $templateProvider = Mockery::mock(PatternProvider::class);
        $templateProvider->shouldReceive('allPatternIds')->andReturn([]);

        $documentProvider = Mockery::mock(DocumentProvider::class);
        $documentProvider->shouldReceive('allDocumentIds')->andReturn([]);

        $primer = new Primer($templateRenderer, $patternProvider, $templateProvider, $documentProvider);
        $primer->setCustomData('foo', 'bar');

        $this->assertSame('<body><p>Document</p></body>', $primer->renderPatterns('frontend/overview'));
    }

    /** @test */
    public function custom_set_data_is_applied_by_renderPattern()
    {
        $templateRenderer = Mockery::mock(TemplateRenderer::class);
        $templateRenderer
            ->shouldReceive('renderPatterns')
            ->once()
            ->withArgs(function (array $patterns, Menu $menu, array $primerData) {
                $this->assertSame('bar', $primerData['foo']);

                return true;
            })
            ->andReturn('<body><p>Document</p></body>');

        $patternProvider = Mockery::mock(PatternProvider::class);
        $patternProvider
            ->shouldReceive('allPatternIds')->andReturn(['frontend/overview'])
            ->shouldReceive('getPattern')->andReturn(new Pattern('frontend/overview', [], ''));

        $templateProvider = Mockery::mock(PatternProvider::class);
        $templateProvider->shouldReceive('allPatternIds')->andReturn([]);

        $documentProvider = Mockery::mock(DocumentProvider::class);
        $documentProvider->shouldReceive('allDocumentIds')->andReturn([]);

        $primer = new Primer($templateRenderer, $patternProvider, $templateProvider, $documentProvider);
        $primer->setCustomData('foo', 'bar');

        $this->assertSame('<body><p>Document</p></body>', $primer->renderPattern('frontend/overview'));
    }

    /** @test */
    public function custom_set_data_is_applied_by_renderTemplate()
    {
        $templateRenderer = Mockery::mock(TemplateRenderer::class);
        $templateRenderer
            ->shouldReceive('renderTemplate')
            ->once()
            ->withArgs(function (Pattern $patterns, array $primerData) {
                $this->assertSame('bar', $primerData['foo']);

                return true;
            })
            ->andReturn('<body><p>Document</p></body>');

        $patternProvider = Mockery::mock(PatternProvider::class);
        $patternProvider->shouldReceive('allPatternIds')->andReturn([]);

        $templateProvider = Mockery::mock(PatternProvider::class);
        $templateProvider
            ->shouldReceive('allPatternIds')->andReturn(['frontend/overview'])
            ->shouldReceive('getPattern')->andReturn(new Pattern('frontend/overview', [], ''));

        $documentProvider = Mockery::mock(DocumentProvider::class);
        $documentProvider->shouldReceive('allDocumentIds')->andReturn([]);

        $primer = new Primer($templateRenderer, $patternProvider, $templateProvider, $documentProvider);
        $primer->setCustomData('foo', 'bar');

        $this->assertSame('<body><p>Document</p></body>', $primer->renderTemplate('frontend/overview'));
    }

    /** @test */
    public function custom_set_data_is_applied_by_renderPatternWithoutChrome()
    {
        $templateRenderer = Mockery::mock(TemplateRenderer::class);
        $templateRenderer
            ->shouldReceive('renderPatternWithoutChrome')
            ->once()
            ->withArgs(function (Pattern $patterns, array $primerData) {
                $this->assertSame('bar', $primerData['foo']);

                return true;
            })
            ->andReturn('<body><p>Document</p></body>');

        $patternProvider = Mockery::mock(PatternProvider::class);
        $patternProvider
            ->shouldReceive('allPatternIds')->andReturn(['frontend/overview'])
            ->shouldReceive('getPattern')->andReturn(new Pattern('frontend/overview', [], ''));

        $templateProvider = Mockery::mock(PatternProvider::class);
        $templateProvider->shouldReceive('allPatternIds')->andReturn([]);

        $documentProvider = Mockery::mock(DocumentProvider::class);
        $documentProvider->shouldReceive('allDocumentIds')->andReturn([]);

        $primer = new Primer($templateRenderer, $patternProvider, $templateProvider, $documentProvider);
        $primer->setCustomData('foo', 'bar');

        $this->assertSame('<body><p>Document</p></body>', $primer->renderPatternWithoutChrome('frontend/overview'));
    }

    /** @test */
    public function can_get_the_current_pattern_id_without_chrome()
    {
        $templateRenderer = Mockery::mock(TemplateRenderer::class);
        $templateRenderer
            ->shouldReceive('renderPatternWithoutChrome')
            ->once();

        $patternProvider = Mockery::mock(PatternProvider::class);
        $patternProvider->shouldReceive('getPattern');

        $templateProvider = Mockery::mock(PatternProvider::class);
        $documentProvider = Mockery::mock(DocumentProvider::class);

        $primer = new Primer($templateRenderer, $patternProvider, $templateProvider, $documentProvider);

        $primer->renderPatternWithoutChrome('components/misc/header');

        $this->assertSame('components/misc/header', $primer->currentPatternId());
    }

    /** @test */
    public function can_get_the_current_pattern_id()
    {
        $templateRenderer = Mockery::mock(TemplateRenderer::class);

        $templateRenderer
            ->shouldReceive('renderPatterns')
            ->once();

        $patternProvider = Mockery::mock(PatternProvider::class);
        $patternProvider
            ->shouldReceive('getPattern')->with('components/misc/header', 'default')->andReturn(new Pattern('components/misc/header', [], ''))
            ->shouldReceive('allPatternIds')->once();

        $templateProvider = Mockery::mock(PatternProvider::class);
        $templateProvider->shouldReceive('allPatternIds')->andReturn([]);

        $documentProvider = Mockery::mock(DocumentProvider::class);
        $documentProvider->shouldReceive('allDocumentIds')->once()->andReturn([]);

        $primer = new Primer($templateRenderer, $patternProvider, $templateProvider, $documentProvider);

        $primer->renderPattern('components/misc/header');

        $this->assertSame('components/misc/header', $primer->currentPatternId());
    }

    /** @test */
    public function can_get_the_current_pattern_id_for_state()
    {
        $templateRenderer = Mockery::mock(TemplateRenderer::class);

        $templateRenderer
            ->shouldReceive('renderPatterns')
            ->once();

        $patternProvider = Mockery::mock(PatternProvider::class);
        $patternProvider
            ->shouldReceive('getPattern')->with('components/misc/header', 'error')->andReturn(new Pattern('components/misc/header', [], '', 'error'))
            ->shouldReceive('allPatternIds')->once();

        $templateProvider = Mockery::mock(PatternProvider::class);
        $templateProvider->shouldReceive('allPatternIds')->andReturn([]);

        $documentProvider = Mockery::mock(DocumentProvider::class);
        $documentProvider->shouldReceive('allDocumentIds')->once()->andReturn([]);

        $primer = new Primer($templateRenderer, $patternProvider, $templateProvider, $documentProvider);

        $primer->renderPattern('components/misc/header', 'error');

        $this->assertSame('components/misc/header', $primer->currentPatternId());
    }

    /** @test */
    public function can_get_the_current_pattern_id_for_state_without_chrome()
    {
        $templateRenderer = Mockery::mock(TemplateRenderer::class);

        $templateRenderer->shouldReceive('renderPatternWithoutChrome')->once();

        $patternProvider = Mockery::mock(PatternProvider::class);
        $patternProvider->shouldReceive('getPattern')->once();

        $templateProvider = Mockery::mock(PatternProvider::class);
        $documentProvider = Mockery::mock(DocumentProvider::class);

        $primer = new Primer($templateRenderer, $patternProvider, $templateProvider, $documentProvider);

        $primer->renderPatternWithoutChrome('components/misc/header', 'error');

        $this->assertSame('components/misc/header', $primer->currentPatternId());
    }

    /** @test */
    public function can_get_the_current_pattern_state_default()
    {
        $templateRenderer = Mockery::mock(TemplateRenderer::class);

        $templateRenderer
            ->shouldReceive('renderPatterns')
            ->once();

        $patternProvider = Mockery::mock(PatternProvider::class);
        $patternProvider
            ->shouldReceive('getPattern')->with('components/misc/header', 'default')->andReturn(new Pattern('components/misc/header', [], '', ''))
            ->shouldReceive('allPatternIds')->once();

        $templateProvider = Mockery::mock(PatternProvider::class);
        $templateProvider->shouldReceive('allPatternIds')->andReturn([]);

        $documentProvider = Mockery::mock(DocumentProvider::class);
        $documentProvider->shouldReceive('allDocumentIds')->once()->andReturn([]);

        $primer = new Primer($templateRenderer, $patternProvider, $templateProvider, $documentProvider);

        $primer->renderPattern('components/misc/header');

        $this->assertSame('default', $primer->currentPatternState());
    }

    /** @test */
    public function can_get_the_current_pattern_state()
    {
        $templateRenderer = Mockery::mock(TemplateRenderer::class);

        $templateRenderer
            ->shouldReceive('renderPatterns')
            ->once();

        $patternProvider = Mockery::mock(PatternProvider::class);
        $patternProvider
            ->shouldReceive('getPattern')->with('components/misc/header', 'error')->andReturn(new Pattern('components/misc/header', [], '', 'error'))
            ->shouldReceive('allPatternIds')->once();

        $templateProvider = Mockery::mock(PatternProvider::class);
        $templateProvider->shouldReceive('allPatternIds')->andReturn([]);

        $documentProvider = Mockery::mock(DocumentProvider::class);
        $documentProvider->shouldReceive('allDocumentIds')->once()->andReturn([]);

        $primer = new Primer($templateRenderer, $patternProvider, $templateProvider, $documentProvider);

        $primer->renderPattern('components/misc/header', 'error');

        $this->assertSame('error', $primer->currentPatternState());
    }

    /** @test */
    public function can_get_the_current_pattern_state_default_without_chrome()
    {
        $templateRenderer = Mockery::mock(TemplateRenderer::class);

        $templateRenderer->shouldReceive('renderPatternWithoutChrome')->once();

        $patternProvider = Mockery::mock(PatternProvider::class);
        $patternProvider->shouldReceive('getPattern')->once();

        $templateProvider = Mockery::mock(PatternProvider::class);
        $documentProvider = Mockery::mock(DocumentProvider::class);

        $primer = new Primer($templateRenderer, $patternProvider, $templateProvider, $documentProvider);

        $primer->renderPatternWithoutChrome('components/misc/header');

        $this->assertSame('default', $primer->currentPatternState());
    }

    /** @test */
    public function can_get_the_current_pattern_state_without_chrome()
    {
        $templateRenderer = Mockery::mock(TemplateRenderer::class);

        $templateRenderer->shouldReceive('renderPatternWithoutChrome')->once();

        $patternProvider = Mockery::mock(PatternProvider::class);
        $patternProvider->shouldReceive('getPattern')->once();

        $templateProvider = Mockery::mock(PatternProvider::class);
        $documentProvider = Mockery::mock(DocumentProvider::class);

        $primer = new Primer($templateRenderer, $patternProvider, $templateProvider, $documentProvider);

        $primer->renderPatternWithoutChrome('components/misc/header', 'error');

        $this->assertSame('error', $primer->currentPatternState());
    }

    protected function assertUIVisible(array $data)
    {
        $this->assertTrue(isset($data['ui']));
        $this->assertTrue($data['ui']);
    }

    protected function assertMenu(Menu $menu, string $selectedId, array $documentIds, array $patternIds, array $templateIds)
    {
        $menuData = $menu->toArray();

        $this->assertTreeArrayData($documentIds, $selectedId, $menuData['documents']['nodes']);
        $this->assertTreeArrayData($patternIds, $selectedId, $menuData['patterns']['nodes']);
        $this->assertTreeArrayData($templateIds, $selectedId, $menuData['templates']['nodes']);
    }

    protected function assertTreeArrayData($ids, $current, $actual)
    {
        $this->assertSame($this->getTreeArrayData($ids, $current), $actual);
    }

    protected function getTreeArrayData($ids, $selected = null): array
    {
        $tree = new Tree($ids);

        if ($selected) {
            try {
                $tree->setCurrent($selected);
            } catch (\Exception $e) {
            }
        }

        return $tree->toArray();
    }
}
