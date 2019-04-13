<?php

namespace Rareloop\Primer;

class IdHelpers
{
    public static function title(string $id) : string
    {
        $parts = explode('/', trim($id, '/'));
        return ucwords(str_replace('-', ' ', $parts[count($parts) - 1]));
    }
}
