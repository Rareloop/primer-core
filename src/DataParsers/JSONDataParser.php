<?php

namespace Rareloop\Primer\DataParsers;

use Rareloop\Primer\Contracts\DataParser;

class JSONDataParser implements DataParser
{
    public function parse($data, string $format) : array
    {
        $output = json_decode($data, true);

        return $output ?: [];
    }

    public function supportedFormats() : array
    {
        return [ 'json' ];
    }
}
