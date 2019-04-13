<?php

namespace Rareloop\Primer\Test;

use PHPUnit\Framework\TestCase;
use Rareloop\Primer\Pattern;

class PatternTest extends TestCase
{
    /** @test */
    public function title_is_created_from_id()
    {
        $pattern1 = new Pattern('components/misc/header', [], '');
        $pattern2 = new Pattern('components/misc/header-with-hyphen', [], '');

        $this->assertSame('Header', $pattern1->title());
        $this->assertSame('Header With Hyphen', $pattern2->title());
    }

    /** @test */
    public function can_get_template()
    {
        $pattern = new Pattern('components/misc/header', [], '<p>{{ title }}</p>');

        $this->assertSame('<p>{{ title }}</p>', $pattern->template());
    }

    /** @test */
    public function template_is_in_array_output()
    {
        $pattern = new Pattern('components/misc/header', [], '<p>{{ title }}</p>');
        $data = $pattern->toArray();

        $this->assertSame('<p>{{ title }}</p>', $data['template']);
    }
}
