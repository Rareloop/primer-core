<?php

namespace Rareloop\Primer\Test\Twig;

use Mockery;
use Twig\Parser;
use Twig\Source;
use Twig\Environment;
use PHPUnit\Framework\TestCase;
use Rareloop\Primer\Twig\IncludePatternNode;
use Twig\Loader\LoaderInterface;
use Rareloop\Primer\Twig\PrimerTokenParser;

class PrimerTokenParserTest extends TestCase
{
    use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    protected $twig;

    public function setUp()
    {
        $this->twig = new Environment($this->createMock(LoaderInterface::class));
        $this->twig->addTokenParser(new PrimerTokenParser);
        $this->parser = new Parser($this->twig);
    }

    /** @test */
    public function can_parse_a_pattern_token()
    {
        $stream = $this->tokenize("{% primer pattern 'components/folder/id' %}");

        $nodes = $this->parser->parse($stream);
        $firstNode = $nodes->getNode('body')->getNode(0);

        $this->assertInstanceOf(IncludePatternNode::class, $firstNode);
        $this->assertSame('components/folder/id', $firstNode->getNode('expr')->getAttribute('value'));
        $this->assertFalse($firstNode->getAttribute('hideUI'));
    }

    /** @test */
    public function can_parse_a_pattern_token_without_ui()
    {
        $stream = $this->tokenize("{% primer pattern 'components/folder/id' hide ui %}");

        $nodes = $this->parser->parse($stream);
        $firstNode = $nodes->getNode('body')->getNode(0);

        $this->assertInstanceOf(IncludePatternNode::class, $firstNode);
        $this->assertSame('components/folder/id', $firstNode->getNode('expr')->getAttribute('value'));
        $this->assertTrue($firstNode->getAttribute('hideUI'));
    }

    private function tokenize(string $code)
    {
        return $this->twig->tokenize(new Source($code, 'test'));
    }
}
