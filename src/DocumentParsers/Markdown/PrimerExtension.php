<?php

namespace Rareloop\Primer\DocumentParsers\Markdown;

use League\CommonMark\Block\Parser\IndentedCodeParser;
use League\CommonMark\Extension\CommonMarkCoreExtension;

class PrimerExtension extends CommonMarkCoreExtension
{
    public function getBlockParsers()
    {
        $parsers = array_filter(parent::getBlockParsers(), function ($parser) {
            return !($parser instanceof IndentedCodeParser);
        });

        return $parsers;
    }
}
