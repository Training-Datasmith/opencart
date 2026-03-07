<?php

declare(strict_types=1);

namespace Todaymade\Daux\Format\Base;

interface Page
{
    /**
     * Get the converted content, without any template.
     *
     * @return string
     */
    public function getPureContent();

    /**
     * Get the full content.
     *
     * @return mixed
     */
    public function getContent();
}
