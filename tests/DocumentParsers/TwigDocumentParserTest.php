<?php

namespace Rareloop\Primer\Test\DataParsers;

use Mockery;
use Twig\Environment;
use Rareloop\Primer\Document;
use PHPUnit\Framework\TestCase;
use Rareloop\Primer\DocumentParsers\TwigDocumentParser;
use Rareloop\Primer\DocumentParsers\YAMLDocumentParser;
use Twig\Template;
use Twig\TemplateWrapper;

class TwigDocumentParserTest extends TestCase
{
    /** @test */
    public function can_parse_twig_from_content()
    {
        $doc = new Document('id', 'Twig Input');
        $doc->setMeta(['foo' => 'bar']);

        $twig = Mockery::mock(Environment::class);
        $template = Mockery::mock(Template::class);
        $templateWrapper = new TemplateWrapper($twig, $template);

        $template->shouldReceive('render')->with($doc->meta())->once()->andReturn('Twig Output');

        $twig->shouldReceive('createTemplate')->with('Twig Input')->once()->andReturn($templateWrapper);

        $parser = new TwigDocumentParser($twig);

        $outputDoc = $parser->parse($doc);

        $this->assertSame('Twig Output', $outputDoc->content());
    }
}
