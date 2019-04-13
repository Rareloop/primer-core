<?php

namespace Rareloop\Primer\Test;

use PHPUnit\Framework\TestCase;
use Rareloop\Primer\Tree;

class TreeTest extends TestCase
{
    /** @test */
    public function can_create_a_tree_from_a_list_of_patterns()
    {
        $tree = new Tree([
            'components/cards/media/image',
            'components/cards/media/video',
            'components/cards/other/other',
            'components/misc/footer',
            'components/misc/header',
            'components/teasers/media',
            'elements/forms/input'
        ]);

        $this->assertSame([
            [
                'id' => 'components',
                'name' => 'Components',
                'ancestor' => false,
                'current' => false,
                'children' => [
                    [
                        'id' => 'components/cards',
                        'name' => 'Cards',
                        'ancestor' => false,
                        'current' => false,
                        'children' => [
                            [
                                'id' => 'components/cards/media',
                                'name' => 'Media',
                                'ancestor' => false,
                                'current' => false,
                                'children' => [
                                    [
                                        'id' => 'components/cards/media/image',
                                        'name' => 'Image',
                                        'ancestor' => false,
                                        'current' => false,
                                        'children' => [],
                                    ],
                                    [
                                        'id' => 'components/cards/media/video',
                                        'name' => 'Video',
                                        'ancestor' => false,
                                        'current' => false,
                                        'children' => [],
                                    ],
                                ],
                            ],
                            [
                                'id' => 'components/cards/other',
                                'name' => 'Other',
                                'ancestor' => false,
                                'current' => false,
                                'children' => [
                                    [
                                        'id' => 'components/cards/other/other',
                                        'name' => 'Other',
                                        'ancestor' => false,
                                        'current' => false,
                                        'children' => [],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    [
                        'id' => 'components/misc',
                        'name' => 'Misc',
                        'ancestor' => false,
                        'current' => false,
                        'children' => [
                            [
                                'id' => 'components/misc/footer',
                                'name' => 'Footer',
                                'ancestor' => false,
                                'current' => false,
                                'children' => [],
                            ],
                            [
                                'id' => 'components/misc/header',
                                'name' => 'Header',
                                'ancestor' => false,
                                'current' => false,
                                'children' => [],
                            ],
                        ],
                    ],
                    [
                        'id' => 'components/teasers',
                        'name' => 'Teasers',
                        'ancestor' => false,
                        'current' => false,
                        'children' => [
                            [
                                'id' => 'components/teasers/media',
                                'name' => 'Media',
                                'ancestor' => false,
                                'current' => false,
                                'children' => [],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'id' => 'elements',
                'name' => 'Elements',
                'ancestor' => false,
                'current' => false,
                'children' => [
                    [
                        'id' => 'elements/forms',
                        'name' => 'Forms',
                        'ancestor' => false,
                        'current' => false,
                        'children' => [
                            [
                                'id' => 'elements/forms/input',
                                'name' => 'Input',
                                'ancestor' => false,
                                'current' => false,
                                'children' => [],
                            ]
                        ],
                    ],
                ],
            ],
        ], $tree->toArray());
    }

    /** @test */
    public function can_handle_groups_at_same_level_as_leaf_nodes()
    {
        $tree = new Tree([
            'components/common/footer',
            'components/common/header',
            'components/misc/another-group/test',
            'components/misc/something-else',
        ]);

        $this->assertSame([
            [
                'id' => 'components',
                'name' => 'Components',
                'ancestor' => false,
                'current' => false,
                'children' => [
                    [
                        'id' => 'components/common',
                        'name' => 'Common',
                        'ancestor' => false,
                        'current' => false,
                        'children' => [
                            [
                                'id' => 'components/common/footer',
                                'name' => 'Footer',
                                'ancestor' => false,
                                'current' => false,
                                'children' => [],
                            ],
                            [
                                'id' => 'components/common/header',
                                'name' => 'Header',
                                'ancestor' => false,
                                'current' => false,
                                'children' => [],
                            ],
                        ],
                    ],
                    [
                        'id' => 'components/misc',
                        'name' => 'Misc',
                        'ancestor' => false,
                        'current' => false,
                        'children' => [
                            [
                                'id' => 'components/misc/another-group',
                                'name' => 'Another Group',
                                'ancestor' => false,
                                'current' => false,
                                'children' => [
                                    [
                                        'id' => 'components/misc/another-group/test',
                                        'name' => 'Test',
                                        'ancestor' => false,
                                        'current' => false,
                                        'children' => [],
                                    ],
                                ],
                            ],
                            [
                                'id' => 'components/misc/something-else',
                                'name' => 'Something Else',
                                'ancestor' => false,
                                'current' => false,
                                'children' => [],
                            ],
                        ],
                    ],
                ],
            ],
        ], $tree->toArray());
    }

    /** @test */
    public function can_mark_a_pattern_as_current()
    {
        $tree = new Tree([
            'components/misc/footer',
            'components/misc/header',
        ]);

        $tree->setCurrent('components/misc/header');

        $this->assertSame([
            [
                'id' => 'components',
                'name' => 'Components',
                'ancestor' => true,
                'current' => false,
                'children' => [
                    [
                        'id' => 'components/misc',
                        'name' => 'Misc',
                        'ancestor' => true,
                        'current' => false,
                        'children' => [
                            [
                                'id' => 'components/misc/footer',
                                'name' => 'Footer',
                                'ancestor' => false,
                                'current' => false,
                                'children' => [],
                            ],
                            [
                                'id' => 'components/misc/header',
                                'name' => 'Header',
                                'ancestor' => false,
                                'current' => true,
                                'children' => [],
                            ],
                        ],
                    ],
                ],
            ],
        ], $tree->toArray());
    }

    /** @test */
    public function can_mark_a_non_pattern_as_current()
    {
        $tree = new Tree([
            'components/misc/footer',
            'components/misc/header',
        ]);

        $tree->setCurrent('components/misc');

        $this->assertSame([
            [
                'id' => 'components',
                'name' => 'Components',
                'ancestor' => true,
                'current' => false,
                'children' => [
                    [
                        'id' => 'components/misc',
                        'name' => 'Misc',
                        'ancestor' => false,
                        'current' => true,
                        'children' => [
                            [
                                'id' => 'components/misc/footer',
                                'name' => 'Footer',
                                'ancestor' => false,
                                'current' => false,
                                'children' => [],
                            ],
                            [
                                'id' => 'components/misc/header',
                                'name' => 'Header',
                                'ancestor' => false,
                                'current' => false,
                                'children' => [],
                            ],
                        ],
                    ],
                ],
            ],
        ], $tree->toArray());
    }

    /** @test */
    public function setCurrent_ignores_leading_and_trailing_slashes()
    {
        $tree = new Tree([
            'components/misc/footer',
            'components/misc/header',
        ]);

        $tree->setCurrent('/components/misc/header/');

        $this->assertSame([
            [
                'id' => 'components',
                'name' => 'Components',
                'ancestor' => true,
                'current' => false,
                'children' => [
                    [
                        'id' => 'components/misc',
                        'name' => 'Misc',
                        'ancestor' => true,
                        'current' => false,
                        'children' => [
                            [
                                'id' => 'components/misc/footer',
                                'name' => 'Footer',
                                'ancestor' => false,
                                'current' => false,
                                'children' => [],
                            ],
                            [
                                'id' => 'components/misc/header',
                                'name' => 'Header',
                                'ancestor' => false,
                                'current' => true,
                                'children' => [],
                            ],
                        ],
                    ],
                ],
            ],
        ], $tree->toArray());
    }

    /**
     * @test
     * @expectedException Rareloop\Primer\Exceptions\TreeNodeNotFoundException
     */
    public function setCurrent_throws_exception_when_node_not_found()
    {
        $tree = new Tree([
            'components/misc/footer',
            'components/misc/header',
        ]);

        $tree->setCurrent('components/not/found');
    }

    /** @test */
    public function can_get_a_count_of_the_number_of_patterns()
    {
        $tree = new Tree([
            'components/cards/media/image',
            'components/cards/media/video',
            'components/cards/other/other',
            'components/misc/footer',
            'components/misc/header',
            'components/teasers/media',
            'elements/forms/input'
        ]);

        $this->assertSame(7, $tree->count());
    }

    /** @test */
    public function can_get_all_leaf_node_ids()
    {
        $tree = new Tree([
            'components/cards/media/image',
            'components/cards/media/video',
            'components/cards/other/other',
            'components/misc/footer',
            'components/misc/header',
            'components/teasers/media',
            'elements/forms/input'
        ]);

        $this->assertSame([
            'components/cards/media/image',
            'components/cards/media/video',
            'components/cards/other/other',
            'components/misc/footer',
            'components/misc/header',
            'components/teasers/media',
            'elements/forms/input'
        ], $tree->getIds());
    }
}
