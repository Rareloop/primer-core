<?php

namespace Rareloop\Primer\Test;

use PHPUnit\Framework\TestCase;
use Rareloop\Primer\Menu;
use Rareloop\Primer\Tree;

class MenuTest extends TestCase
{
    /** @test */
    public function can_add_sections_to_a_menu()
    {
        $menu = new Menu();
        $tree = new Tree([
            'components/misc/header',
            'components/misc/footer',
        ]);

        $menu->addSection('patterns', $tree);

        $this->assertSame([
            'patterns' => [
                'ancestor' => false,
                'nodes' => $tree->toArray(),
            ],
        ], $menu->toArray());
    }

    /** @test */
    public function can_get_a_section()
    {
        $menu = new Menu();
        $tree = new Tree([
            'components/misc/header',
            'components/misc/footer',
        ]);

        $menu->addSection('patterns', $tree);

        $this->assertSame($tree, $menu->getSection('patterns'));
    }

    /** @test */
    public function can_test_if_a_section_exists()
    {
        $menu = new Menu();
        $tree = new Tree([
            'components/misc/header',
            'components/misc/footer',
        ]);

        $menu->addSection('patterns', $tree);

        $this->assertTrue($menu->hasSection('patterns'));
        $this->assertFalse($menu->hasSection('templates'));
    }

    /** @test */
    public function getSection_throws_an_exception_if_section_is_not_found()
    {
        $this->expectException(\Rareloop\Primer\Exceptions\SectionNotFoundException::class);

        $menu = new Menu();
        $menu->getSection('patterns');
    }

    /** @test */
    public function addSection_is_chainable()
    {
        $menu = new Menu();

        $this->assertSame($menu, $menu->addSection('patterns', new Tree([])));
    }

    /** @test */
    public function can_set_current_item()
    {
        $menu = new Menu();

        $tree = new Tree([
            'components/misc/footer',
            'components/misc/header',
        ]);

        $menu->addSection('patterns', $tree);
        $menu->setCurrent('patterns', 'components/misc/footer');

        $data = $menu->toArray();

        $this->assertTrue($data['patterns']['ancestor']);

        $this->assertTrue($data['patterns']['nodes'][0]['ancestor']);
        $this->assertFalse($data['patterns']['nodes'][0]['current']);
        $this->assertTrue($data['patterns']['nodes'][0]['children'][0]['ancestor']);
        $this->assertFalse($data['patterns']['nodes'][0]['children'][0]['current']);
        $this->assertFalse($data['patterns']['nodes'][0]['children'][0]['children'][0]['ancestor']);
        $this->assertTrue($data['patterns']['nodes'][0]['children'][0]['children'][0]['current']);
    }

    /** @test */
    public function setCurrent_is_chainable()
    {
        $menu = new Menu();

        $tree = new Tree([
            'components/misc/footer',
            'components/misc/header',
        ]);

        $menu->addSection('patterns', $tree);
        $this->assertSame($menu, $menu->setCurrent('patterns', 'components/misc/footer'));
    }
}
