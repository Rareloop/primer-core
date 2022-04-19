<?php

namespace Rareloop\Primer\Test;

use Mockery;
use PHPUnit\Framework\TestCase;
use Rareloop\Primer\Contracts\DocumentParser;
use Rareloop\Primer\Document;
use Rareloop\Primer\FileSystemDocumentProvider;
use org\bovigo\vfs\vfsStream;

class FileSystemDocumentProviderTest extends TestCase
{
    /** @test */
    public function can_get_all_documents_from_a_single_load_point()
    {
        vfsStream::setup();
        $root = vfsStream::create([
            'foo' => [
                'bar' => [
                    'abstract.md' => '',
                    'frontend' => [
                        'building.md' => '',
                        'updating.md' => '',
                    ],
                    'overview.md' => '',
                ],
            ],
        ]);

        $dataProvider = new FileSystemDocumentProvider([vfsStream::url('root/foo/bar')], 'md');

        $this->assertSame(['abstract', 'frontend/building', 'frontend/updating', 'overview'], $dataProvider->allDocumentIds());
    }

    /** @test */
    public function can_get_all_patterns_from_multiple_load_point()
    {
        vfsStream::setup();
        $root = vfsStream::create([
            'foo1' => [
                'bar1' => [
                    'abstract.md' => '',
                    'frontend' => [
                        'building.md' => '',
                        'updating.md' => '',
                    ],
                    'overview.md' => '',
                ],
            ],
            'foo2' => [
                'bar2' => [
                    'another.md' => '',
                    'frontend' => [
                        'zebra.md' => '',
                    ],
                ],
            ],
        ]);

        $dataProvider = new FileSystemDocumentProvider([vfsStream::url('root/foo1/bar1'), vfsStream::url('root/foo2/bar2')], 'md');

        $this->assertSame([
            'abstract',
            'another',
            'frontend/building',
            'frontend/updating',
            'frontend/zebra',
            'overview',
        ], $dataProvider->allDocumentIds());
    }

    /** @test */
    public function can_order_all_document_ids_using_numeric_prefix()
    {
        vfsStream::setup();
        $root = vfsStream::create([
            'foo' => [
                'bar' => [
                    '03-abstract.md' => '',
                    '01-frontend' => [
                        '02-building.md' => '',
                        '01-updating.md' => '',
                    ],
                    '02-overview.md' => '',
                ],
            ],
        ]);

        $dataProvider = new FileSystemDocumentProvider([vfsStream::url('root/foo/bar')], 'md');

        $this->assertSame(['frontend/updating', 'frontend/building', 'overview', 'abstract'], $dataProvider->allDocumentIds());
    }

    /** @test */
    public function can_get_document()
    {
        vfsStream::setup();
        $root = vfsStream::create([
            'foo' => [
                'bar' => [
                    'abstract.md' => '',
                    'frontend' => [
                        'building.md' => 'testing123',
                        'updating.md' => '',
                    ],
                    'overview.md' => '',
                ],
            ],
        ]);

        $dataProvider = new FileSystemDocumentProvider([vfsStream::url('root/foo/bar')], 'md');
        $pattern = $dataProvider->getDocument('frontend/building');

        $this->assertInstanceOf(Document::class, $pattern);
        $this->assertSame('frontend/building', $pattern->id());
        $this->assertSame('testing123', $pattern->content());
    }

    /** @test */
    public function can_get_document_with_a_content_parser()
    {
        vfsStream::setup();
        $root = vfsStream::create([
            'foo' => [
                'bar' => [
                    'abstract.md' => '',
                    'frontend' => [
                        'building.md' => 'testing123',
                        'updating.md' => '',
                    ],
                    'overview.md' => '',
                ],
            ],
        ]);

        $documentParser = Mockery::mock(DocumentParser::class);
        $documentParser->shouldReceive('parse')->withArgs(function (Document $doc) {
            return $doc->id() === 'frontend/building' && $doc->content() === 'testing123';
        })->andReturn(new Document('frontend/building', 'testing456'));

        $dataProvider = new FileSystemDocumentProvider([vfsStream::url('root/foo/bar')], 'md', $documentParser);
        $pattern = $dataProvider->getDocument('frontend/building');

        $this->assertInstanceOf(Document::class, $pattern);
        $this->assertSame('frontend/building', $pattern->id());
        $this->assertSame('testing456', $pattern->content());
    }

    /** @test */
    public function can_get_document_that_is_numerically_ordered()
    {
        vfsStream::setup();
        $root = vfsStream::create([
            'foo' => [
                'bar' => [
                    'abstract.md' => '',
                    '02-frontend' => [
                        '01-building.md' => '01-building.md',
                        'updating.md' => '',
                    ],
                    '01-overview.md' => '01-overview.md',
                ],
            ],
        ]);

        $dataProvider = new FileSystemDocumentProvider([vfsStream::url('root/foo/bar')], 'md');
        $pattern1 = $dataProvider->getDocument('overview');
        $pattern2 = $dataProvider->getDocument('frontend/building');

        $this->assertSame('overview', $pattern1->id());
        $this->assertSame('01-overview.md', $pattern1->content());
        $this->assertSame('frontend/building', $pattern2->id());
        $this->assertSame('01-building.md', $pattern2->content());
    }

    /** @test */
    public function getDocument_throws_exception_if_document_does_not_exist()
    {
        $this->expectException(\Rareloop\Primer\Exceptions\DocumentNotFoundException::class);
        vfsStream::setup();
        $root = vfsStream::create([
            'foo' => [
                'bar' => [
                    'abstract.md' => '',
                ],
            ],
        ]);

        $dataProvider = new FileSystemDocumentProvider([vfsStream::url('root/foo/bar')], 'md');

        $dataProvider->getDocument('not-abstract');
    }

    /** @test */
    public function allDocumentIds_does_not_fall_over_if_no_paths_are_provided()
    {
        $dataProvider = new FileSystemDocumentProvider([], 'md');

        $this->assertSame([], $dataProvider->allDocumentIds());
    }

    /** @test */
    public function getDocument_does_not_fall_over_if_no_paths_are_provided()
    {
        $this->expectException(\Rareloop\Primer\Exceptions\DocumentNotFoundException::class);

        $dataProvider = new FileSystemDocumentProvider([], 'md');

        $dataProvider->getDocument('not/found');
    }
}
