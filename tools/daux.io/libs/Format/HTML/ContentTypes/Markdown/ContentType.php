<?php namespace Todaymade\Daux\Format\HTML\ContentTypes\Markdown;

class ContentType extends \Todaymade\Daux\ContentTypes\Markdown\ContentType
{
    protected function createConverter(): \Todaymade\Daux\ContentTypes\Markdown\CommonMarkConverter
    {
        return new CommonMarkConverter(['daux' => $this->config]);
    }
}
