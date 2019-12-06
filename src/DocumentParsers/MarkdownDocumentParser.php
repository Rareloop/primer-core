<?php

namespace Rareloop\Primer\DocumentParsers;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use Rareloop\Primer\DocumentParsers\Markdown\PrimerExtension;
use Rareloop\Primer\Contracts\DocumentParser;
use Rareloop\Primer\Document;

class MarkdownDocumentParser implements DocumentParser
{
    public function parse(Document $document): Document
    {
        $converter = new CommonMarkConverter([], $this->createEnvironment());
        $document->setContent($converter->convertToHtml($document->content()));

        return $document;
    }

    protected function createEnvironment(): Environment
    {
        $environment = new Environment();
        $environment->addExtension(new PrimerExtension());
        $environment->mergeConfig([
            'renderer' => [
                'block_separator' => "\n",
                'inner_separator' => "\n",
                'soft_break'      => "\n",
            ],
            'safe'               => false, // deprecated option
            'html_input'         => Environment::HTML_INPUT_ALLOW,
            'allow_unsafe_links' => true,
            'max_nesting_level'  => INF,
        ]);

        return $environment;
    }
}
