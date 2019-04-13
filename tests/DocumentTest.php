<?php

namespace Rareloop\Primer\Test;

use PHPUnit\Framework\TestCase;
use Rareloop\Primer\Document;

class DocumentTest extends TestCase
{
    /** @test */
    public function by_default_title_is_set_based_on_the_id()
    {
        $doc1 = new Document('id', 'content');
        $doc2 = new Document('the-title', 'content');

        $this->assertSame('Id', $doc1->title());
        $this->assertSame('The Title', $doc2->title());
    }

    /** @test */
    public function can_set_metadata()
    {
        $doc = new Document('id', 'content');

        $doc->setMeta(['foo' => 'bar']);

        $this->assertSame(['foo' => 'bar'], $doc->meta());
    }

    /** @test */
    public function can_set_title()
    {
        $doc = new Document('id', 'content');

        $doc->setTitle('title');

        $this->assertSame('title', $doc->title());
    }

    /** @test */
    public function can_set_description()
    {
        $doc = new Document('id', 'content');

        $doc->setDescription('description');

        $this->assertSame('description', $doc->description());
    }

    /** @test */
    public function can_convert_to_array()
    {
        $doc = new Document('id', 'content');
        $doc->setTitle('title');
        $doc->setDescription('description');
        $doc->setMeta(['foo' => 'bar']);

        $this->assertSame([
            'id' => 'id',
            'content' => 'content',
            'title' => 'title',
            'description' => 'description',
            'meta' => ['foo' => 'bar'],
        ], $doc->toArray());
    }
}
