<?php

namespace Rareloop\Primer\Test\DataParsers;

use Mockery;
use PHPUnit\Framework\TestCase;
use Rareloop\Primer\Contracts\DocumentParser;
use Rareloop\Primer\Document;
use Rareloop\Primer\DocumentParsers\ChainDocumentParser;

class ChainDocumentParserTest extends TestCase
{
    /** @test */
    public function can_chain_multiple_parsers()
    {
        $parser1 = Mockery::mock(DocumentParser::class);
        $parser1->shouldReceive('parse')->withArgs(function (Document $doc) {
            return $doc->id() === 'id' && $doc->content() === 'A';
        })->once()->andReturn(new Document('id', 'B'));

        $parser2 = Mockery::mock(DocumentParser::class);
        $parser2->shouldReceive('parse')->withArgs(function (Document $doc) {
            return $doc->id() === 'id' && $doc->content() === 'B';
        })->once()->andReturn(new Document('id', 'C'));

        $chainParser = new ChainDocumentParser([$parser1, $parser2]);

        $outputDoc = $chainParser->parse(new Document('id', 'A'));

        $this->assertSame('id', $outputDoc->id());
        $this->assertSame('C', trim($outputDoc->content()));
    }
}
