<?php

declare(strict_types=1);
/**
 * Created by IntelliJ IDEA.
 * User: onigoetz
 * Date: 06/11/15
 * Time: 20:27.
 */

namespace Todaymade\Daux\Format\Base;

use Todaymade\Daux\DauxHelper;
use Todaymade\Daux\Tree\Content;
use Todaymade\Daux\Tree\Root;

class EmbedImages
{
    public function __construct(protected \Todaymade\Daux\Tree\Root $tree)
    {
    }

    public function embed($page, Content $file, $callback): string|array|null
    {
        return preg_replace_callback(
            "/<img\\s+[^>]*src=['\"]([^\"]*)['\"][^>]*>/",
            function (array $matches) use ($file, $callback) {
                if ($result = $this->findImage($matches[1], $matches[0], $file, $callback)) {
                    return $result;
                }

                return $matches[0];
            },
            $page
        );
    }

    /**
     * @return mixed[]
     */
    private function getAttributes(string $tag): array
    {
        $dom = new \DOMDocument();
        $dom->loadHTML($tag);

        $img = $dom->getElementsByTagName('img')->item(0);

        $attributes = ['align', 'class', 'title', 'style', 'alt', 'height', 'width'];
        $used = [];
        foreach ($attributes as $attr) {
            if ($img->attributes->getNamedItem($attr)) {
                $used[$attr] = $img->attributes->getNamedItem($attr)->value;
            }
        }

        return $used;
    }

    private function findImage(string $src, string $tag, Content $file, $callback)
    {
        // for protocol relative or http requests : keep the original one
        if (str_starts_with($src, 'http') || str_starts_with($src, '//')) {
            return $src;
        }

        // Get the path to the file, relative to the root of the documentation
        $url = DauxHelper::getCleanPath(dirname($file->getUrl()) . '/' . $src);

        // Get any file corresponding to the right one
        $file = DauxHelper::getFile($this->tree, $url);

        if ($file === false) {
            return false;
        }

        $result = $callback($src, $this->getAttributes($tag), $file);

        return $result ?: $src;
    }
}
