<?php

namespace Rareloop\Primer\Contracts;

interface DataParser
{
    public function parse($data, string $format) : array;

    public function supportedFormats() : array;
}
