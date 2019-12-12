<?php

namespace Rareloop\Primer\Twig;

use Twig\Compiler;
use Twig\Node\Node;
use Twig\Node\NodeOutputInterface;
use Twig\Node\Expression\AbstractExpression;

class IncludePatternNode extends Node implements NodeOutputInterface
{
    public function __construct(AbstractExpression $expr, bool $hideUI = false, int $lineno, string $tag = null)
    {
        $nodes = ['expr' => $expr];

        parent::__construct($nodes, ['hideUI' => (bool) $hideUI], $lineno, $tag);
    }

    public function compile(Compiler $compiler)
    {
        $compiler->addDebugInfo($this);

        // phpcs:disable Generic.Files.LineLength

        if ($this->getAttribute('hideUI') === true) {
            $compiler
                ->write('$this->loadTemplate(')
                ->subcompile($this->getNode('expr'))
                ->raw(', ')
                ->repr($this->getTemplateName())
                ->raw(', ')
                ->repr($this->getTemplateLine())
                ->raw(')')
                ->raw('->display(\Rareloop\Primer\Twig\PrimerExtension::primer()->getPatternStateData(')
                ->subcompile($this->getNode('expr'))
                ->raw('));');

            return;
        }

        $compiler
            ->write('$this->loadTemplate("primer-pattern.twig", ')
            ->repr($this->getTemplateName())
            ->raw(', ')
            ->repr($this->getTemplateLine())
            ->raw(')')
            ->raw('->display(["pattern" => \Rareloop\Primer\Twig\PrimerExtension::primer()->patternProvider()->getPattern(')
            ->subcompile($this->getNode('expr'))
            ->raw(')->toArray(), "primer" => \Rareloop\Primer\Twig\PrimerExtension::primer()->getCustomData()]);');
        // phpcs:enable Generic.Files.LineLength
    }
}
