<?php

declare(strict_types=1);

namespace Todaymade\Daux;

class GeneratorHelper
{
    /**
     * Remove a directory recursively.
     *
     * @param string $dir
     */
    public static function rmdir($dir): void
    {
        $it = new \RecursiveDirectoryIterator($dir);
        $files = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($files as $file) {
            if ($file->getFilename() === '.') {
                continue;
            }
            if ($file->getFilename() === '..') {
                continue;
            }
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
    }

    /**
     * Copy files recursively.
     */
    public static function copyRecursive(string $source, string $destination): void
    {
        if (!is_dir($destination)) {
            mkdir($destination);
        }

        $dir = opendir($source);

        if ($dir === false) {
            throw new Exception("Cannot copy '$source' to '$destination'");
        }

        while (false !== ($file = readdir($dir))) {
            if ($file != '.' && $file != '..') {
                if (is_dir($source . DIRECTORY_SEPARATOR . $file)) {
                    static::copyRecursive(
                        $source . DIRECTORY_SEPARATOR . $file,
                        $destination . DIRECTORY_SEPARATOR . $file
                    );
                } else {
                    copy($source . DIRECTORY_SEPARATOR . $file, $destination . DIRECTORY_SEPARATOR . $file);
                }
            }
        }
        closedir($dir);
    }
}
