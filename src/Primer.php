<?php

namespace Rareloop\Primer;

use Rareloop\Primer\Contracts\DocumentProvider;
use Rareloop\Primer\Contracts\DocumentRenderer;
use Rareloop\Primer\Contracts\PatternProvider;
use Rareloop\Primer\Contracts\TemplateRenderer;
use Rareloop\Primer\Exceptions\PatternNotFoundException;
use Rareloop\Primer\Exceptions\TreeNodeNotFoundException;

class Primer
{
    protected $templateRenderer;
    protected $documentRenderer;
    protected $patternProvider;
    protected $templateProvider;
    protected $documentProvider;

    public function __construct(
        TemplateRenderer $templateRenderer,
        PatternProvider $patternProvider,
        PatternProvider $templateProvider,
        DocumentProvider $documentProvider
    ) {
        $this->templateRenderer = $templateRenderer;
        $this->patternProvider = $patternProvider;
        $this->templateProvider = $templateProvider;
        $this->documentProvider = $documentProvider;
    }

    public function renderPatternWithoutChrome(string $id, string $state = 'default') : string
    {
        $pattern = $this->patternProvider->getPattern($id, $state);

        return $this->templateRenderer->renderPatternWithoutChrome($pattern, [
            'title' => IdHelpers::title($id),
        ]);
    }

    public function renderTemplate(string $id, string $state = 'default') : string
    {
        $pattern = $this->templateProvider->getPattern($id, $state);

        return $this->templateRenderer->renderPatternWithoutChrome($pattern, [
            'title' => IdHelpers::title($id),
        ]);
    }

    public function renderPattern(string $id, string $state = 'default') : string
    {
        $pattern = $this->patternProvider->getPattern($id, $state);

        return $this->templateRenderer->renderPatterns(
            [ $pattern ],
            $this->getMenu()->setCurrent('patterns', $id),
            [
                'ui' => true,
                'title' => $pattern->title(),
            ]
        );
    }

    public function renderPatterns(string $id) : string
    {
        $patterns = collect($this->patternProvider->allPatternIds())
            ->filter(function ($thisId) use ($id) {
                return strpos($thisId, $id) === 0;
            })->map(function ($thisId) {
                return $this->patternProvider->getPattern($thisId);
            })->all();

        if (count($patterns) === 0) {
            throw new PatternNotFoundException;
        }

        return $this->templateRenderer->renderPatterns($patterns, $this->getMenu()->setCurrent('patterns', $id), [
            'ui' => true,
            'title' => IdHelpers::title($id),
        ]);
    }

    public function renderDocument(string $id) : string
    {
        $document = $this->documentProvider->getDocument($id);

        return $this->templateRenderer->renderDocument($document, $this->getMenu()->setCurrent('documents', $id), [
            'ui' => true,
            'title' => $document->title(),
            'description' => $document->description(),
        ]);
    }

    public function getMenu() : Menu
    {
        $patternIds = $this->patternProvider->allPatternIds();
        $templateIds = $this->templateProvider->allPatternIds();
        $documentIds = $this->documentProvider->allDocumentIds();

        $Tree = new Tree($patternIds);
        $templateTree = new Tree($templateIds);
        $documentTree = new Tree($documentIds);

        $menu = new Menu;
        $menu
            ->addSection('documents', $documentTree)
            ->addSection('patterns', $Tree)
            ->addSection('templates', $templateTree);

        return $menu;
    }
}
