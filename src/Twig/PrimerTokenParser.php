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
            $hideUI = false;

            if ($stream->nextIf(Token::NAME_TYPE, 'hide')) {
                $stream->expect(Token::NAME_TYPE, 'ui');
                $hideUI = true;
            }

            $node = new IncludePatternNode($expr, $hideUI, $token->getLine(), $this->getTag());
        }

        $stream->expect(Token::BLOCK_END_TYPE);

        return $node;
    }

    public function getTag()
    {
        return 'primer';
    }
}
