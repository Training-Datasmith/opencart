<?php namespace Todaymade\Daux\Format\Base;

use Todaymade\Daux\Tree\ComputedRaw;

abstract class ComputedRawPage implements Page
{
    public function __construct(protected \Todaymade\Daux\Tree\ComputedRaw $raw)
    {
    }

    public function getFilename()
    {
        return $this->raw->getUri();
    }

    public function getContent()
    {
        return $this->raw->getContent();
    }

    public function getPureContent()
    {
        return $this->raw->getContent();
    }
}
