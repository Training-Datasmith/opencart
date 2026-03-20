<?php

declare(strict_types=1);

namespace Todaymade\Daux\Format\Base;

use Todaymade\Daux\Exception;

abstract class RawPage implements Page
{
    public function __construct(protected $file)
    {
    }

    public function getFile()
    {
        return $this->file;
    }

    public function getPureContent()
    {
        throw new Exception('you should not use getPureContent() to show a raw content');
    }

    public function getContent()
    {
        throw new Exception('you should not use getContent() to show a raw content');
    }
}
