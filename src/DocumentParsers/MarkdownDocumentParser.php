<?php

namespace Rareloop\Primer\DocumentParsers;

use League\CommonMark\CommonMarkConverter;
use Rareloop\Primer\Contracts\DocumentParser;
use Rareloop\Primer\Document;

class MarkdownDocumentParser implements DocumentParser
{
    public function parse(Document $document) : Document
    {
        $converter = new CommonMarkConverter();
        $document->setContent($converter->convertToHtml($document->content()));

        return $document;
    }
}
