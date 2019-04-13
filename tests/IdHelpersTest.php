<?php

namespace Rareloop\Primer\Test;

use PHPUnit\Framework\TestCase;
use Rareloop\Primer\IdHelpers;

class IdHelpersTest extends TestCase
{
    /** @test */
    public function can_get_title()
    {
        $this->assertSame('Header', IdHelpers::title('components/misc/header'));
        $this->assertSame('Header Block', IdHelpers::title('components/misc/header-block'));
    }
}
