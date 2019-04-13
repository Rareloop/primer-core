<?php

namespace Rareloop\Primer\Contracts;

use Rareloop\Primer\Document;
use Rareloop\Primer\Menu;
use Rareloop\Primer\Pattern;

interface TemplateRenderer
{
    public function renderPatternWithoutChrome(Pattern $pattern, array $primerData = []) : string;

    public function renderPatterns(array $patterns, Menu $menu, array $primerData = []) : string;

    public function renderDocument(Document $doc, Menu $menu, array $primerData = []) : string;
}
