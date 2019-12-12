<?php

namespace Rareloop\Primer\Twig;

use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;
use Rareloop\Primer\Twig\IncludePatternNode;

class PrimerTokenParser extends AbstractTokenParser
{
    public function parse(Token $token)
    {
        $stream = $this->parser->getStream();

        $node = null;

        if ($stream->nextIf(Token::NAME_TYPE, 'pattern')) {
            $expr = $this->parser->getExpressionParser()->parseExpression();
            $node = new IncludePatternNode($expr, $token->getLine(), $this->getTag());
        }

        $stream->expect(Token::BLOCK_END_TYPE);

        return $node;
    }

    public function getTag()
    {
        return 'primer';
    }
}
