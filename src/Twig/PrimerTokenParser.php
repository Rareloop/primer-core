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


        if ($stream->nextIf(Token::NAME_TYPE, 'pattern')) {
            $node = null;
            $expr = $this->parser->getExpressionParser()->parseExpression();
            $hideUI = false;
            $state = 'default';

            if ($stream->nextIf(Token::NAME_TYPE, 'with')) {
                $stream->expect(Token::NAME_TYPE, 'state');
                $token = $stream->expect(Token::STRING_TYPE);
                $state = $token->getValue();
            }

            if ($stream->nextIf(Token::NAME_TYPE, 'hide')) {
                $stream->expect(Token::NAME_TYPE, 'ui');
                $hideUI = true;
            }

            $node = new IncludePatternNode($expr, $token->getLine(), $state, $hideUI, $this->getTag());
        }

        $stream->expect(Token::BLOCK_END_TYPE);

        return $node;
    }

    public function getTag()
    {
        return 'primer';
    }
}
