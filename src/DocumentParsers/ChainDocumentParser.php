<?php

namespace Rareloop\Primer\DocumentParsers;

use Rareloop\Primer\Contracts\DocumentParser;
use Rareloop\Primer\Document;

class ChainDocumentParser implements DocumentParser
{
    protected $parsers = [];

    public function __construct(array $parsers)
    {
        foreach ($parsers as $parser) {
            $this->addParser($parser);
        }
    }

    protected function addParser(DocumentParser $parser)
    {
        $this->parsers[] = $parser;
    }

    public function parse(Document $document) : Document
    {
        return collect($this->parsers)->reduce(function ($doc, $parser) {
            return $parser->parse($doc);
        }, $document);
    }
}
