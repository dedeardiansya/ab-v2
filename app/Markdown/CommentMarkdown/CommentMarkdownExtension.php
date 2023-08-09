<?php

namespace App\Markdown\CommentMarkdown;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\CommonMark\Delimiter\Processor\EmphasisDelimiterProcessor;
use League\CommonMark\Extension\CommonMark\Node;
use League\CommonMark\Extension\CommonMark\Parser;
use League\CommonMark\Extension\CommonMark\Renderer;
use League\CommonMark\Extension\ExtensionInterface;
use League\CommonMark\Node as CoreNode;
use League\CommonMark\Parser as CoreParser;
use League\CommonMark\Renderer as CoreRenderer;

class CommentMarkdownExtension implements ExtensionInterface
{
    public function register(EnvironmentBuilderInterface $environment): void
    {
        $environment
            ->addBlockStartParser(new Parser\Block\FencedCodeStartParser(), 50)
            ->addBlockStartParser(new Parser\Block\HtmlBlockStartParser(), 40)
            ->addBlockStartParser(new Parser\Block\ListBlockStartParser(), 10)
            ->addBlockStartParser(new Parser\Block\IndentedCodeStartParser(), -100)

            ->addInlineParser(new CoreParser\Inline\NewlineParser(), 200)
            ->addInlineParser(new Parser\Inline\BacktickParser(), 150)
            ->addInlineParser(new Parser\Inline\EscapableParser(), 80)
            ->addInlineParser(new Parser\Inline\EntityParser(), 70)
            ->addInlineParser(new Parser\Inline\AutolinkParser(), 50)
            ->addInlineParser(new Parser\Inline\HtmlInlineParser(), 40)
            ->addInlineParser(new Parser\Inline\CloseBracketParser(), 30)
            ->addInlineParser(new Parser\Inline\OpenBracketParser(), 20)
            ->addInlineParser(new Parser\Inline\BangParser(), 10)

            ->addRenderer(CoreNode\Block\Document::class, new CoreRenderer\Block\DocumentRenderer(), 0)
            ->addRenderer(Node\Block\FencedCode::class, new Renderer\Block\FencedCodeRenderer(), 0)
            ->addRenderer(Node\Block\HtmlBlock::class, new Renderer\Block\HtmlBlockRenderer(), 0)
            ->addRenderer(Node\Block\IndentedCode::class, new Renderer\Block\IndentedCodeRenderer(), 0)
            ->addRenderer(Node\Block\ListBlock::class, new Renderer\Block\ListBlockRenderer(), 0)
            ->addRenderer(Node\Block\ListItem::class, new Renderer\Block\ListItemRenderer(), 0)
            ->addRenderer(CoreNode\Block\Paragraph::class, new CoreRenderer\Block\ParagraphRenderer(), 0)

            ->addRenderer(Node\Inline\Code::class, new Renderer\Inline\CodeRenderer(), 0)
            ->addRenderer(Node\Inline\Emphasis::class, new Renderer\Inline\EmphasisRenderer(), 0)
            ->addRenderer(Node\Inline\HtmlInline::class, new Renderer\Inline\HtmlInlineRenderer(), 0)
            ->addRenderer(Node\Inline\Image::class, new Renderer\Inline\ImageRenderer(), 0)
            ->addRenderer(Node\Inline\Link::class, new Renderer\Inline\LinkRenderer(), 0)
            ->addRenderer(CoreNode\Inline\Newline::class, new CoreRenderer\Inline\NewlineRenderer(), 0)
            ->addRenderer(Node\Inline\Strong::class, new Renderer\Inline\StrongRenderer(), 0)
            ->addRenderer(CoreNode\Inline\Text::class, new CoreRenderer\Inline\TextRenderer(), 0)

            ->addDelimiterProcessor(new EmphasisDelimiterProcessor('*'))
            ->addDelimiterProcessor(new EmphasisDelimiterProcessor('_'));
    }
}
