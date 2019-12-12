<?php

namespace Rareloop\Primer\Twig;

use Rareloop\Primer\Contracts\PatternProvider;
use Rareloop\Primer\Primer;
use Twig\Extension\GlobalsInterface;
use Twig\Extension\AbstractExtension;
use Rareloop\Primer\Twig\PrimerTokenParser;

class PrimerExtension extends AbstractExtension
{
    protected static $primer;

    public static function primer()
    {
        return static::$primer;
    }

    public function __construct(Primer $primer)
    {
        static::$primer = $primer;
    }

    public function getTokenParsers()
    {
        return [new PrimerTokenParser];
    }
}
