<?php

declare(strict_types=1);

namespace Todaymade\Daux\Format\HTMLFile\ContentTypes\Markdown;

class ContentType extends \Todaymade\Daux\ContentTypes\Markdown\ContentType
{
    protected function createConverter(): \Todaymade\Daux\Format\HTMLFile\ContentTypes\Markdown\CommonMarkConverter
    {
        return new CommonMarkConverter(['daux' => $this->config]);
    }
}
