<?php

namespace Rareloop\Primer\DocumentParsers;

use Mni\FrontYAML\Parser;
use Rareloop\Primer\Contracts\DocumentParser;
use Rareloop\Primer\Document;
use Spatie\YamlFrontMatter\YamlFrontMatter;

class YAMLDocumentParser implements DocumentParser
{
    public function parse(Document $document) : Document
    {
        $parser = new Parser;
        $parsedDocument = $parser->parse($document->content(), false);
        $yaml = $parsedDocument->getYAML();

        $newDoc = new Document($document->id(), $parsedDocument->getContent());
        $newDoc->setMeta($yaml);

        if (!empty($yaml['title'])) {
            $newDoc->setTitle($yaml['title']);
        }

        $newDoc->setDescription($yaml['description'] ?? '');

        return $newDoc;
    }
}
