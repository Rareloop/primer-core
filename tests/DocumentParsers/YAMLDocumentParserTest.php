<?php

namespace Rareloop\Primer\Test\DataParsers;

use PHPUnit\Framework\TestCase;
use Rareloop\Primer\Document;
use Rareloop\Primer\DocumentParsers\YAMLDocumentParser;

class YAMLDocumentParserTest extends TestCase
{
    /** @test */
    public function can_parse_yaml_front_matter()
    {
        $parser = new YAMLDocumentParser;

        $content = [
            '---',
            'title: Title',
            'description: Description',
            '---',
            'Content',
        ];

        $doc = new Document('id', implode("\n", $content));
        $outputDoc = $parser->parse($doc);

        $this->assertSame('Content', trim($outputDoc->content()));
        $this->assertSame([
            'title' => 'Title',
            'description' => 'Description',
        ], $outputDoc->meta());

        $this->assertSame('Title', $outputDoc->title());
        $this->assertSame('Description', $outputDoc->description());
    }

    /** @test */
    public function title_is_not_overwritten_if_not_in_front_matter()
    {
        $parser = new YAMLDocumentParser;

        $content = [
            '---',
            'description: Description',
            '---',
            'Content',
        ];

        $doc = new Document('original-title', implode("\n", $content));
        $outputDoc = $parser->parse($doc);

        $this->assertSame('Original Title', $outputDoc->title());
    }
}
