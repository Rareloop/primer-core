<?php

namespace Rareloop\Primer\Test\DataParsers;

use PHPUnit\Framework\TestCase;
use Rareloop\Primer\DataParsers\JSONDataParser;

class JSONDataParserTest extends TestCase
{
    /** @test */
    public function json_is_a_supported_format()
    {
        $parser = new JSONDataParser;

        $this->assertContains('json', $parser->supportedFormats());
    }

    /** @test */
    public function can_parse_json()
    {
        $parser = new JSONDataParser;

        $this->assertSame([ 'foo' => 'bar' ], $parser->parse('{ "foo": "bar"}', 'json'));
    }
}
