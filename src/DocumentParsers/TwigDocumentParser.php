<?php

namespace Rareloop\Primer\DocumentParsers;

use Rareloop\Primer\Contracts\DocumentParser;
use Rareloop\Primer\Document;
use Spatie\YamlFrontMatter\YamlFrontMatter;
use Twig\Environment;

class TwigDocumentParser implements DocumentParser
{
    protected $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function parse(Document $document) : Document
    {
        $template = $this->twig->createTemplate(trim($document->content()));
        $document->setContent($template->render($document->meta()));

        return $document;
    }
}
