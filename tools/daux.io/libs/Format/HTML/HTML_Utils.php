<?php

declare(strict_types=1);

namespace Todaymade\Daux\Format\HTML;

use Todaymade\Daux\GeneratorHelper;

trait HTMLUtils
{
    public function ensureEmptyDestination($destination): void
    {
        if (is_dir($destination)) {
            GeneratorHelper::rmdir($destination);
        } else {
            mkdir($destination, 0777, true);
        }
    }

    /**
     * Copy all files from $local to $destination.
     *
     * @param string $localBase
     */
    public function copyThemes(string $destination, $localBase): void
    {
        mkdir($destination . DIRECTORY_SEPARATOR . 'themes');
        GeneratorHelper::copyRecursive(
            $localBase,
            $destination . DIRECTORY_SEPARATOR . 'themes'
        );
    }
}
