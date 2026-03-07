<?php namespace Todaymade\Daux\ContentTypes\Markdown\Admonition;

use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Node\Block\Paragraph;

class AdmonitionBlock extends AbstractBlock
{
    public function __construct(private string $type, private ?Paragraph $title)
    {
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getTitle(): ?Paragraph
    {
        return $this->title;
    }
}
