<?php

namespace Rareloop\Primer\Test;

use Mockery;
use PHPUnit\Framework\TestCase;
use Rareloop\Primer\FileSystemPatternProvider;
use Rareloop\Primer\DataParsers\JSONDataParser;
use Rareloop\Primer\Pattern;
use org\bovigo\vfs\vfsStream;

class FileSystemPatternProviderTest extends TestCase
{
    /** @test */
    public function can_get_all_patterns_from_a_single_load_point()
    {
        vfsStream::setup();
        $root = vfsStream::create([
            'foo' => [
                'bar' => [
                    'components' => [
                        'misc' => [
                            'header' => [
                                'template.twig' => '<header>Hello World</header>',
                            ],
                            'footer' => [
                                'template.twig' => '<footer>Hello World</footer>',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $dataProvider = new FileSystemPatternProvider([vfsStream::url('root/foo/bar')], 'twig');

        $this->assertSame(['components/misc/footer', 'components/misc/header'], $dataProvider->allPatternIds());
    }

    /** @test */
    public function can_get_all_patterns_from_multiple_load_point()
    {
        vfsStream::setup();
        $root = vfsStream::create([
            'foo1' => [
                'bar1' => [
                    'components' => [
                        'misc' => [
                            'header1' => [
                                'template.twig' => '<header>Hello World</header>',
                            ],
                            'footer1' => [
                                'template.twig' => '<footer>Hello World</footer>',
                            ],
                        ],
                    ],
                ],
            ],
            'foo2' => [
                'bar2' => [
                    'components' => [
                        'misc' => [
                            'header2' => [
                                'template.twig' => '<header>Hello World</header>',
                            ],
                            'footer2' => [
                                'template.twig' => '<footer>Hello World</footer>',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $dataProvider = new FileSystemPatternProvider([vfsStream::url('root/foo1/bar1'), vfsStream::url('root/foo2/bar2')], 'twig');

        $this->assertSame([
            'components/misc/footer1',
            'components/misc/footer2',
            'components/misc/header1',
            'components/misc/header2',
        ], $dataProvider->allPatternIds());
    }

    /** @test */
    public function can_get_template_contents()
    {
        vfsStream::setup();
        $root = vfsStream::create([
            'foo' => [
                'bar' => [
                    'components' => [
                        'misc' => [
                            'header' => [
                                'template.twig' => '<header>Hello World</header>',
                            ],
                            'footer' => [
                                'template.twig' => '<footer>Hello World</footer>',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $dataProvider = new FileSystemPatternProvider([vfsStream::url('root/foo/bar')], 'twig');

        $this->assertSame('<header>Hello World</header>', $dataProvider->getPatternTemplate('components/misc/header'));
        $this->assertSame('<footer>Hello World</footer>', $dataProvider->getPatternTemplate('components/misc/footer'));
    }

    /**
     * @test
     * @expectedException Rareloop\Primer\Exceptions\PatternNotFoundException
     */
    public function can_not_get_template_contents_if_partial_id_provided()
    {
        vfsStream::setup();
        $root = vfsStream::create([
            'foo' => [
                'bar' => [
                    'components' => [
                        'misc' => [
                            'header' => [
                                'template.twig' => '<header>Hello World</header>',
                            ],
                            'footer' => [
                                'template.twig' => '<footer>Hello World</footer>',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $dataProvider = new FileSystemPatternProvider([vfsStream::url('root/foo/bar')], 'twig');

        $dataProvider->getPatternTemplate('misc/header');
    }

    /** @test */
    public function can_check_if_a_pattern_exists()
    {
        vfsStream::setup();
        $root = vfsStream::create([
            'foo' => [
                'bar' => [
                    'components' => [
                        'misc' => [
                            'header' => [
                                'template.twig' => '<header>Hello World</header>',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $dataProvider = new FileSystemPatternProvider([vfsStream::url('root/foo/bar')], 'twig');

        $this->assertTrue($dataProvider->patternExists('components/misc/header'));
        $this->assertFalse($dataProvider->patternExists('components/misc/footer'));
    }

    /** @test */
    public function can_check_if_state_data_exists_for_a_pattern_as_json()
    {
        vfsStream::setup();
        $root = vfsStream::create([
            'foo' => [
                'bar' => [
                    'components' => [
                        'misc' => [
                            'header' => [
                                'template.twig' => '<header>Hello World</header>',
                                'data.json' => '{ "foo": "bar" }',
                                'data~error.json' => '{ "foo": "bar" }',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $dataProvider = new FileSystemPatternProvider([vfsStream::url('root/foo/bar')], 'twig', new JSONDataParser);

        $this->assertTrue($dataProvider->patternHasState('components/misc/header'));
        $this->assertTrue($dataProvider->patternHasState('components/misc/header', 'error'));
        $this->assertFalse($dataProvider->patternHasState('components/misc/header', 'unexpected'));
    }

    /** @test */
    public function can_check_if_state_data_exists_for_a_pattern_as_php()
    {
        vfsStream::setup();
        $root = vfsStream::create([
            'foo' => [
                'bar' => [
                    'components' => [
                        'misc' => [
                            'header' => [
                                'template.twig' => '<header>Hello World</header>',
                                'data.php' => '<?php return [ \'foo\' => \'bar\'];',
                                'data~error.php' => '<?php return [ \'foo\' => \'bar\'];',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $dataProvider = new FileSystemPatternProvider([vfsStream::url('root/foo/bar')], 'twig');

        $this->assertTrue($dataProvider->patternHasState('components/misc/header', 'error'));
        $this->assertFalse($dataProvider->patternHasState('components/misc/header', 'unexpected'));
    }

    /**
     * @test
     * @expectedException Rareloop\Primer\Exceptions\PatternNotFoundException
     */
    public function patternHasState_throws_exception_if_pattern_does_not_exist()
    {
        vfsStream::setup();
        $root = vfsStream::create([
            'foo' => [
                'bar' => [
                    'components' => [
                        'misc' => [
                            'header' => [
                                'template.twig' => '<header>Hello World</header>',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $dataProvider = new FileSystemPatternProvider([vfsStream::url('root/foo/bar')], 'twig');

        $dataProvider->patternHasState('components/misc/footer', 'error');
    }

    /** @test */
    public function can_get_pattern()
    {
        vfsStream::setup();
        $root = vfsStream::create([
            'foo' => [
                'bar' => [
                    'components' => [
                        'misc' => [
                            'header' => [
                                'template.twig' => '<header>Hello World</header>',
                                'data.php' => '<?php return [\'foo\' => \'bar\'];',
                                'data~error.php' => '<?php return [\'foo1\' => \'bar1\'];',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $dataProvider = new FileSystemPatternProvider([vfsStream::url('root/foo/bar')], 'twig');
        $pattern = $dataProvider->getPattern('components/misc/header');

        $this->assertInstanceOf(Pattern::class, $pattern);
        $this->assertSame('components/misc/header', $pattern->id());
        $this->assertSame(['foo' => 'bar'], $pattern->data());
        $this->assertSame('default', $pattern->state());
        $this->assertSame(['default', 'error'], $pattern->states());
    }

    /** @test */
    public function can_get_correct_pattern_when_name_is_a_prefix_to_another()
    {
        vfsStream::setup();
        $root = vfsStream::create([
            'foo' => [
                'bar' => [
                    'components' => [
                        'misc' => [
                            'headers' => [
                                'template.twig' => '<header>Not Hello World</header>',
                                'data.php' => '<?php return [\'bar\' => \'baz\'];',
                            ],
                            'header' => [
                                'template.twig' => '<header>Hello World</header>',
                                'data.php' => '<?php return [\'foo\' => \'bar\'];',
                                'data~error.php' => '<?php return [\'foo1\' => \'bar1\'];',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $dataProvider = new FileSystemPatternProvider([vfsStream::url('root/foo/bar')], 'twig');
        $pattern = $dataProvider->getPattern('components/misc/header');

        $this->assertInstanceOf(Pattern::class, $pattern);
        $this->assertSame('components/misc/header', $pattern->id());
        $this->assertSame(['foo' => 'bar'], $pattern->data());
        $this->assertSame('default', $pattern->state());
        $this->assertSame(['default', 'error'], $pattern->states());
    }

    /** @test */
    public function can_get_pattern_with_non_default_state()
    {
        vfsStream::setup();
        $root = vfsStream::create([
            'foo' => [
                'bar' => [
                    'components' => [
                        'misc' => [
                            'header' => [
                                'template.twig' => '<header>Hello World</header>',
                                'data.php' => '<?php return [\'foo\' => \'bar\'];',
                                'data~error.php' => '<?php return [\'foo1\' => \'bar1\'];',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $dataProvider = new FileSystemPatternProvider([vfsStream::url('root/foo/bar')], 'twig');
        $pattern = $dataProvider->getPattern('components/misc/header', 'error');

        $this->assertInstanceOf(Pattern::class, $pattern);
        $this->assertSame('components/misc/header', $pattern->id());
        $this->assertSame(['foo1' => 'bar1'], $pattern->data());
        $this->assertSame('error', $pattern->state());
        $this->assertSame(['default', 'error'], $pattern->states());
    }





    /** @test */
    public function can_get_state_data_for_pattern_from_json()
    {
        vfsStream::setup();
        $root = vfsStream::create([
            'foo' => [
                'bar' => [
                    'components' => [
                        'misc' => [
                            'header' => [
                                'template.twig' => '<header>Hello World</header>',
                                'data.json' => '{ "foo": "bar" }',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $dataProvider = new FileSystemPatternProvider([vfsStream::url('root/foo/bar')], 'twig', new JSONDataParser);
        $pattern = $dataProvider->getPattern('components/misc/header');

        $this->assertSame(['foo' => 'bar'], $pattern->data());
    }

    /** @test */
    public function dataProvider_is_available_in_php_based_state_data_file()
    {
        vfsStream::setup();
        $root = vfsStream::create([
            'foo' => [
                'bar' => [
                    'components' => [
                        'misc' => [
                            'header' => [
                                'template.twig' => '<header>Hello World</header>',
                                'data.php' => '<?php return [ \'foo1\' => $dataProvider->getPatternStateData(\'components/misc/header\', \'error\')];',
                                'data~error.php' => '<?php return [ \'foo2\' => \'bar2\'];',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $dataProvider = new FileSystemPatternProvider([vfsStream::url('root/foo/bar')], 'twig');

        $pattern = $dataProvider->getPattern('components/misc/header');
        $data = $pattern->data();

        $this->assertTrue(isset($data['foo1']));
        $this->assertSame(['foo2' => 'bar2'], $data['foo1']);
    }

    /** @test */
    public function pattern_uses_default_if_requested_state_does_not_exist()
    {
        vfsStream::setup();
        $root = vfsStream::create([
            'foo' => [
                'bar' => [
                    'components' => [
                        'misc' => [
                            'header' => [
                                'template.twig' => '<header>Hello World</header>',
                                'data.php' => '<?php return [\'foo\' => \'bar\'];',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $dataProvider = new FileSystemPatternProvider([vfsStream::url('root/foo/bar')], 'twig');
        $pattern = $dataProvider->getPattern('components/misc/header', 'error');

        $this->assertSame(['foo' => 'bar'], $pattern->data());
        $this->assertSame('default', $pattern->state());
    }

    /** @test */
    public function pattern_states_array_contains_default_when_data_file_exists()
    {
        vfsStream::setup();
        $root = vfsStream::create([
            'foo' => [
                'bar' => [
                    'components' => [
                        'misc' => [
                            'header' => [
                                'template.twig' => '<header>Hello World</header>',
                                'data.php' => '<?php return [ \'foo2\' => \'bar2\'];',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $dataProvider = new FileSystemPatternProvider([vfsStream::url('root/foo/bar')], 'twig');
        $pattern = $dataProvider->getPattern('components/misc/header');

        $this->assertSame(['default'], $pattern->states());
    }

    /** @test */
    public function pattern_states_array_contains_default_when_no_data_file_exists()
    {
        vfsStream::setup();
        $root = vfsStream::create([
            'foo' => [
                'bar' => [
                    'components' => [
                        'misc' => [
                            'header' => [
                                'template.twig' => '<header>Hello World</header>',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $dataProvider = new FileSystemPatternProvider([vfsStream::url('root/foo/bar')], 'twig');
        $pattern = $dataProvider->getPattern('components/misc/header');

        $this->assertSame(['default'], $pattern->states());
    }

    /** @test */
    public function can_get_last_modified_of_a_template()
    {
        $root = vfsStream::setup();
        vfsStream::create([
            'foo' => [
                'bar' => [
                    'components' => [
                        'misc' => [
                            'header' => [],
                        ],
                    ],
                ],
            ],
        ]);
        vfsStream::newFile('template.twig')->at($root->getChild('foo/bar/components/misc/header'))->lastModified(123456789);
        $dataProvider = new FileSystemPatternProvider([vfsStream::url('root/foo/bar')], 'twig');

        $this->assertSame(123456789, $dataProvider->getPatternTemplateLastModified('components/misc/header'));
    }

    /** @test */
    public function can_get_default_state_data_for_a_pattern()
    {
        vfsStream::setup();
        $root = vfsStream::create([
            'foo' => [
                'bar' => [
                    'components' => [
                        'misc' => [
                            'header' => [
                                'template.twig' => '<header>Hello World</header>',
                                'data.php' => '<?php return [\'foo\' => \'bar\'];',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $dataProvider = new FileSystemPatternProvider([vfsStream::url('root/foo/bar')], 'twig');
        $data = $dataProvider->getPatternStateData('components/misc/header');

        $this->assertSame(['foo' => 'bar'], $data);
    }

    /** @test */
    public function can_get_non_default_state_data_for_a_pattern()
    {
        vfsStream::setup();
        $root = vfsStream::create([
            'foo' => [
                'bar' => [
                    'components' => [
                        'misc' => [
                            'header' => [
                                'template.twig' => '<header>Hello World</header>',
                                'data.php' => '<?php return [\'foo\' => \'bar\'];',
                                'data~error.php' => '<?php return [\'foo1\' => \'bar1\'];',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $dataProvider = new FileSystemPatternProvider([vfsStream::url('root/foo/bar')], 'twig');
        $data = $dataProvider->getPatternStateData('components/misc/header', 'error');

        $this->assertSame(['foo1' => 'bar1'], $data);
    }

    /** @test */
    public function getPatternStateData_returns_empty_array_when_no_data_is_available()
    {
        vfsStream::setup();
        $root = vfsStream::create([
            'foo' => [
                'bar' => [
                    'components' => [
                        'misc' => [
                            'header' => [
                                'template.twig' => '<header>Hello World</header>',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $dataProvider = new FileSystemPatternProvider([vfsStream::url('root/foo/bar')], 'twig');
        $data = $dataProvider->getPatternStateData('components/misc/header', 'error');

        $this->assertSame([], $data);
    }

    /**
     * @test
     * @expectedException Rareloop\Primer\Exceptions\PatternNotFoundException
     */
    public function getPatternStateData_throws_exception_when_pattern_is_invalid()
    {
        vfsStream::setup();
        $root = vfsStream::create([
            'foo' => [
                'bar' => [
                    'components' => [
                        'misc' => [
                            'header' => [
                                'template.twig' => '<header>Hello World</header>',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $dataProvider = new FileSystemPatternProvider([vfsStream::url('root/foo/bar')], 'twig');
        $dataProvider->getPatternStateData('components/misc/not-header', 'error');
    }

    /** @test */
    public function allPatternIds_does_not_fall_over_if_no_paths_are_provided()
    {
        $dataProvider = new FileSystemPatternProvider([], 'twig');

        $this->assertSame([], $dataProvider->allPatternIds());
    }

    /** @test */
    public function patternExists_does_not_fall_over_if_no_paths_are_provided()
    {
        $dataProvider = new FileSystemPatternProvider([], 'twig');

        $this->assertFalse($dataProvider->patternExists('not/found'));
    }

    /** @test */
    public function patternHasState_does_not_fall_over_if_no_paths_are_provided()
    {
        $dataProvider = new FileSystemPatternProvider([], 'twig');

        $this->assertFalse($dataProvider->patternHasState('not/found', 'state'));
    }

    /** @test */
    public function getPatternStateData_does_not_fall_over_if_no_paths_are_provided()
    {
        $dataProvider = new FileSystemPatternProvider([], 'twig');

        $this->assertSame([], $dataProvider->getPatternStateData('not/found', 'state'));
    }

    /**
     * @test
     * @expectedException Rareloop\Primer\Exceptions\PatternNotFoundException
     */
    public function getPattern_does_not_fall_over_if_no_paths_are_provided()
    {
        $dataProvider = new FileSystemPatternProvider([], 'md');

        $dataProvider->getPattern('not/found');
    }
}
