<?php

declare(strict_types=1);

namespace Todaymade\Daux\Extension;

use Todaymade\Daux\Tree\Root;

class Processor extends \Todaymade\Daux\Processor
{
    public function manipulateTree(Root $root): void
    {
        print_r($root->dump());
    }
}
