<?php

namespace Rareloop\Primer\DocumentParsers;

use Rareloop\Primer\Contracts\DocumentParser;
use Rareloop\Primer\Document;
use Spatie\YamlFrontMatter\YamlFrontMatter;

class YAMLDocumentParser implements DocumentParser
{
    public function parse(Document $document): Document
    {
        $parsedDocument = YamlFrontMatter::parse($document->content());

        $newDoc = new Document($document->id(), $parsedDocument->body());
        $newDoc->setMeta($parsedDocument->matter() ?? []);

        if (!empty($parsedDocument->matter('title'))) {
            $newDoc->setTitle($parsedDocument->matter('title'));
        }

        $newDoc->setDescription($parsedDocument->matter('description') ?? '');
        return $newDoc;
    }
}
