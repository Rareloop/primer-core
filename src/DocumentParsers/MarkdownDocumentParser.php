<?php

namespace Rareloop\Primer\DocumentParsers;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Util\HtmlFilter;
use Rareloop\Primer\DocumentParsers\Markdown\PrimerExtension;
use Rareloop\Primer\Contracts\DocumentParser;
use Rareloop\Primer\Document;

class MarkdownDocumentParser implements DocumentParser
{
    public function parse(Document $document): Document
    {
        $converter = new MarkdownConverter($this->createEnvironment());

        $document->setContent(
            $converter->convert($document->content())
        );

        return $document;
    }

    protected function createEnvironment(): Environment
    {
        $environment = new Environment([
            'renderer' => [
                'block_separator' => "\n",
                'inner_separator' => "\n",
                'soft_break'      => "\n",
            ],
            'html_input'         => HtmlFilter::ALLOW,
            'allow_unsafe_links' => true,
            'max_nesting_level'  => PHP_INT_MAX,
        ]);
        $environment->addExtension(new PrimerExtension());

        return $environment;
    }
}
