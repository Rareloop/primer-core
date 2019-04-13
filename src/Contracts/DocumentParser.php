<?php

namespace Rareloop\Primer\Contracts;

use Rareloop\Primer\Document;

interface DocumentParser
{
    public function parse(Document $document) : Document;
}
