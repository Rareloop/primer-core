<?php

namespace Rareloop\Primer\Test\DataParsers;

use Mockery;
use PHPUnit\Framework\TestCase;
use Rareloop\Primer\Document;
use Rareloop\Primer\DocumentParsers\TwigDocumentParser;
use Rareloop\Primer\DocumentParsers\YAMLDocumentParser;
use Twig\Environment;

class TwigDocumentParserTest extends TestCase
{
    /** @test */
    public function can_parse_twig_from_content()
    {
        $doc = new Document('id', 'Twig Input');
        $doc->setMeta(['foo' => 'bar']);

        $template = Mockery::mock(Template::class);
        $template->shouldReceive('render')->with($doc->meta())->once()->andReturn('Twig Output');

        $twig = Mockery::mock(Environment::class);
        $twig->shouldReceive('createTemplate')->with('Twig Input')->once()->andReturn($template);

        $parser = new TwigDocumentParser($twig);

        $outputDoc = $parser->parse($doc);

        $this->assertSame('Twig Output', $outputDoc->content());
    }
}
