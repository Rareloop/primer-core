<?php

namespace Rareloop\Primer\DocumentParsers\Markdown;

use League\CommonMark\Extension\CommonMark\Node\Block\BlockQuote;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\CommonMark\Node\Block\HtmlBlock;
use League\CommonMark\Extension\CommonMark\Node\Block\IndentedCode;
use League\CommonMark\Extension\CommonMark\Node\Block\ListBlock;
use League\CommonMark\Extension\CommonMark\Node\Block\ListItem;
use League\CommonMark\Extension\CommonMark\Node\Block\ThematicBreak;
use League\CommonMark\Extension\CommonMark\Node\Inline\Code;
use League\CommonMark\Extension\CommonMark\Node\Inline\Emphasis;
use League\CommonMark\Extension\CommonMark\Node\Inline\HtmlInline;
use League\CommonMark\Extension\CommonMark\Node\Inline\Image;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Extension\CommonMark\Node\Inline\Strong;
use League\CommonMark\Extension\CommonMark\Parser\Block\BlockQuoteStartParser;
use League\CommonMark\Extension\CommonMark\Parser\Block\FencedCodeStartParser;
use League\CommonMark\Extension\CommonMark\Parser\Block\HeadingStartParser;
use League\CommonMark\Extension\CommonMark\Parser\Block\HtmlBlockStartParser;
use League\CommonMark\Extension\CommonMark\Parser\Block\ListBlockStartParser;
use League\CommonMark\Extension\CommonMark\Parser\Block\ThematicBreakStartParser;
use League\CommonMark\Extension\CommonMark\Parser\Inline\AutolinkParser;
use League\CommonMark\Extension\CommonMark\Parser\Inline\BacktickParser;
use League\CommonMark\Extension\CommonMark\Parser\Inline\BangParser;
use League\CommonMark\Extension\CommonMark\Parser\Inline\CloseBracketParser;
use League\CommonMark\Extension\CommonMark\Parser\Inline\EntityParser;
use League\CommonMark\Extension\CommonMark\Parser\Inline\EscapableParser;
use League\CommonMark\Extension\CommonMark\Parser\Inline\HtmlInlineParser;
use League\CommonMark\Extension\CommonMark\Parser\Inline\OpenBracketParser;
use League\CommonMark\Extension\CommonMark\Renderer\Block\BlockQuoteRenderer;
use League\CommonMark\Extension\CommonMark\Renderer\Block\FencedCodeRenderer;
use League\CommonMark\Extension\CommonMark\Renderer\Block\HeadingRenderer;
use League\CommonMark\Extension\CommonMark\Renderer\Block\HtmlBlockRenderer;
use League\CommonMark\Extension\CommonMark\Renderer\Block\IndentedCodeRenderer;
use League\CommonMark\Extension\CommonMark\Renderer\Block\ListBlockRenderer;
use League\CommonMark\Extension\CommonMark\Renderer\Block\ListItemRenderer;
use League\CommonMark\Extension\CommonMark\Renderer\Block\ThematicBreakRenderer;
use League\CommonMark\Extension\CommonMark\Renderer\Inline\CodeRenderer;
use League\CommonMark\Extension\CommonMark\Renderer\Inline\EmphasisRenderer;
use League\CommonMark\Extension\CommonMark\Renderer\Inline\HtmlInlineRenderer;
use League\CommonMark\Extension\CommonMark\Renderer\Inline\ImageRenderer;
use League\CommonMark\Extension\CommonMark\Renderer\Inline\LinkRenderer;
use League\CommonMark\Extension\CommonMark\Renderer\Inline\StrongRenderer;
use League\CommonMark\Extension\ExtensionInterface;
use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\CommonMark\Delimiter\Processor\EmphasisDelimiterProcessor;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Node\Inline\Newline;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Parser\Inline\NewlineParser;
use League\CommonMark\Renderer\Block\DocumentRenderer;
use League\CommonMark\Renderer\Block\ParagraphRenderer;
use League\CommonMark\Renderer\Inline\NewlineRenderer;
use League\CommonMark\Renderer\Inline\TextRenderer;

/**
 * Primer flavoured Markdown setup. It is the same as the CommonMarkCoreExtension but with the
 * `IndentedCodeStartParser` removed to allow for embedded Twig code in Markdown to be interpretted
 * rather than code blocked.
 */
class PrimerExtension implements ExtensionInterface
{
    public function register(EnvironmentBuilderInterface $environment): void
    {
        $environment
            ->addBlockStartParser(new BlockQuoteStartParser(),     70)
            ->addBlockStartParser(new HeadingStartParser(),        60)
            ->addBlockStartParser(new FencedCodeStartParser(),     50)
            ->addBlockStartParser(new HtmlBlockStartParser(),      40)
            ->addBlockStartParser(new ThematicBreakStartParser(),  20)
            ->addBlockStartParser(new ListBlockStartParser(),      10)

            ->addInlineParser(new NewlineParser(),  200)
            ->addInlineParser(new BacktickParser(),    150)
            ->addInlineParser(new EscapableParser(),    80)
            ->addInlineParser(new EntityParser(),       70)
            ->addInlineParser(new AutolinkParser(),     50)
            ->addInlineParser(new HtmlInlineParser(),   40)
            ->addInlineParser(new CloseBracketParser(), 30)
            ->addInlineParser(new OpenBracketParser(),  20)
            ->addInlineParser(new BangParser(),         10)

            ->addRenderer(BlockQuote::class,    new BlockQuoteRenderer(),    0)
            ->addRenderer(Document::class, new DocumentRenderer(),  0)
            ->addRenderer(FencedCode::class,    new FencedCodeRenderer(),    0)
            ->addRenderer(Heading::class,       new HeadingRenderer(),       0)
            ->addRenderer(HtmlBlock::class,     new HtmlBlockRenderer(),     0)
            ->addRenderer(IndentedCode::class,  new IndentedCodeRenderer(),  0)
            ->addRenderer(ListBlock::class,     new ListBlockRenderer(),     0)
            ->addRenderer(ListItem::class,      new ListItemRenderer(),      0)
            ->addRenderer(Paragraph::class, new ParagraphRenderer(), 0)
            ->addRenderer(ThematicBreak::class, new ThematicBreakRenderer(), 0)

            ->addRenderer(Code::class,        new CodeRenderer(),        0)
            ->addRenderer(Emphasis::class,    new EmphasisRenderer(),    0)
            ->addRenderer(HtmlInline::class,  new HtmlInlineRenderer(),  0)
            ->addRenderer(Image::class,       new ImageRenderer(),       0)
            ->addRenderer(Link::class,        new LinkRenderer(),        0)
            ->addRenderer(Newline::class, new NewlineRenderer(), 0)
            ->addRenderer(Strong::class,      new StrongRenderer(),      0)
            ->addRenderer(Text::class,    new TextRenderer(),    0);


        if ($environment->getConfiguration()->get('commonmark/use_asterisk')) {
            $environment->addDelimiterProcessor(new EmphasisDelimiterProcessor('*'));
        }

        if ($environment->getConfiguration()->get('commonmark/use_underscore')) {
            $environment->addDelimiterProcessor(new EmphasisDelimiterProcessor('_'));
        }
    }
}
c
