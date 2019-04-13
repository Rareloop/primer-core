<?php

namespace Rareloop\Primer\Twig;

use Rareloop\Primer\Contracts\TemplateRenderer;
use Rareloop\Primer\Document;
use Rareloop\Primer\Menu;
use Rareloop\Primer\Pattern;
use Twig\Environment;

class TwigTemplateRenderer implements TemplateRenderer
{
    protected $twig;
    protected $templateFilename = 'primer-template.twig';

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function renderPatternWithoutChrome(Pattern $pattern, array $primerData = []) : string
    {
        $data = [
            'patterns' => [$pattern->toArray()],
            'primer' => $primerData,
        ];

        return $this->twig->render($this->templateFilename, $data);
    }

    public function renderPatterns(array $patterns, Menu $menu, array $primerData = []) : string
    {
        $data = [
            'menu' => $menu->toArray(),
            'patterns' => collect($patterns)->map(function ($item) {
                return $item->toArray();
            })->all(),
            'primer' => $primerData,
        ];

        return $this->twig->render($this->templateFilename, $data);
    }

    public function renderDocument(Document $document, Menu $menu, array $primerData = []) : string
    {
        $data = [
            'menu' => $menu->toArray(),
            'document' => $document->toArray(),
            'primer' => $primerData,
        ];

        return $this->twig->render($this->templateFilename, $data);
    }
}
