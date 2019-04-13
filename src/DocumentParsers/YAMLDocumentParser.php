<?php

namespace Rareloop\Primer\DocumentParsers;

use Rareloop\Primer\Contracts\DocumentParser;
use Rareloop\Primer\Document;
use Spatie\YamlFrontMatter\YamlFrontMatter;

class YAMLDocumentParser implements DocumentParser
{
    public function parse(Document $document) : Document
    {
        $object = YamlFrontMatter::parse($document->content());

        $newDoc = new Document($document->id(), $object->body());
        $newDoc->setMeta($object->matter());

        if (!empty($object->matter('title'))) {
            $newDoc->setTitle($object->matter('title'));
        }

        $newDoc->setDescription($object->matter('description') ?? '');

        return $newDoc;
    }
}
