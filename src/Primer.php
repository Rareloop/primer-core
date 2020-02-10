<?php

namespace Rareloop\Primer;

use Rareloop\Primer\Pattern;
use Rareloop\Primer\Contracts\PatternProvider;
use Rareloop\Primer\Contracts\DocumentProvider;
use Rareloop\Primer\Contracts\TemplateRenderer;
use Rareloop\Primer\Exceptions\PatternNotFoundException;

class Primer
{
    protected $templateRenderer;
    protected $patternProvider;
    protected $templateProvider;
    protected $documentProvider;

    protected $currentPatternId;
    protected $currentPatternState;
    protected $currentTemplateId;
    protected $currentTemplateState;

    protected $customData = [];

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

    public function renderPatternWithoutChrome(string $id, string $state = 'default'): string
    {
        $pattern = $this->getPattern($id, $state);

        return $this->templateRenderer->renderPatternWithoutChrome(
            $pattern,
            $this->getData([
                'title' => IdHelpers::title($id),
                'mode' => 'pattern',
            ])
        );
    }

    public function renderTemplate(string $id, string $state = 'default'): string
    {
        $this->currentTemplateId = $id;
        $this->currentTemplateState = $state;

        $pattern = $this->templateProvider->getPattern($id, $state);

        return $this->templateRenderer->renderTemplate(
            $pattern,
            $this->getData([
                'title' => IdHelpers::title($id),
                'mode' => 'template',
            ])
        );
    }

    public function renderPattern(string $id, string $state = 'default'): string
    {
        $pattern = $this->getPattern($id, $state);

        return $this->templateRenderer->renderPatterns(
            [$pattern],
            $this->getMenu()->setCurrent('patterns', $id),
            $this->getData([
                'ui' => true,
                'mode' => 'pattern',
                'title' => $pattern->title(),
            ])
        );
    }

    protected function getPattern($id, $state = 'default'): Pattern
    {
        $this->currentPatternId = $id;
        $this->currentPatternState = $state;

        return $this->patternProvider->getPattern($id, $state);
    }

    public function renderPatterns(string $id): string
    {
        $patterns = collect($this->patternProvider->allPatternIds())
            ->filter(function ($currentPatternId) use ($id) {
                if (strpos($currentPatternId, $id) !== 0) {
                    return false;
                }

                // We need to make sure that we don't match substrings that are not folder prefixes
                // e.g. we don't want to match `misc/headers` against `misc/header`
                $nextChar = substr($currentPatternId, strlen($id), 1);

                return empty($nextChar) || $nextChar === '/';
            })->map(function ($currentPatternId) {
                return $this->getPattern($currentPatternId);
            })->all();

        if (count($patterns) === 0) {
            throw new PatternNotFoundException;
        }

        return $this->templateRenderer->renderPatterns(
            $patterns,
            $this->getMenu()->setCurrent('patterns', $id),
            $this->getData([
                'ui' => true,
                'mode' => 'pattern',
                'title' => IdHelpers::title($id),
            ])
        );
    }

    public function renderDocument(string $id): string
    {
        $document = $this->documentProvider->getDocument($id);

        return $this->templateRenderer->renderDocument(
            $document,
            $this->getMenu()->setCurrent('documents', $id),
            $this->getData([
                'ui' => true,
                'mode' => 'document',
                'title' => $document->title(),
                'description' => $document->description(),
            ])
        );
    }

    protected function getData(array $data = []): array
    {
        return array_merge($this->customData, $data);
    }

    public function getMenu(): Menu
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

    public function getPatternStateData(string $id, string $state = 'default'): array
    {
        try {
            return $this->patternProvider->getPatternStateData($id, $state);
        } catch (PatternNotFoundException $e) {
            return [];
        }
    }

    public function getTemplateStateData(string $id, string $state = 'default'): array
    {
        try {
            return $this->templateProvider->getPatternStateData($id, $state);
        } catch (PatternNotFoundException $e) {
            return [];
        }
    }

    /**
     * Provide custom data to pass to the twig renderer. Is merged into the `primer` set of data
     *
     * @param string $key
     * @param string|number|array $value
     */
    public function setCustomData(string $key, $value)
    {
        $this->customData[$key] = $value;
    }

    public function currentPatternId(): ?string
    {
        return $this->currentPatternId;
    }

    public function currentPatternState(): ?string
    {
        return $this->currentPatternState;
    }

    public function currentTemplateId(): ?string
    {
        return $this->currentTemplateId;
    }

    public function currentTemplateState(): ?string
    {
        return $this->currentTemplateState;
    }
}
