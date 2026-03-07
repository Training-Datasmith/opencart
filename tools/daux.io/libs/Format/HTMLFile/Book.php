<?php namespace Todaymade\Daux\Format\HTMLFile;

use Todaymade\Daux\Config;
use Todaymade\Daux\Tree\Content;
use Todaymade\Daux\Tree\Directory;

class Book
{
    protected $pages = [];

    public function __construct(protected \Todaymade\Daux\Tree\Directory $tree, Config $config)
    {
        $this->config = $config;
    }

    protected function getStyles(): string
    {
        $styles = '';
        foreach ($this->config->getTheme()->getCSS() as $css) {
            $file = $this->config->getThemesPath() . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . $css;
            $styles .= '<style>' . file_get_contents($file) . '</style>';
        }

        return $styles;
    }

    protected function getPageUrl($page)
    {
        return 'file_' . str_replace('/', '_', $page->getUrl());
    }

    /**
     * @return array{title: (string | null), href: non-falsy-string, children?: mixed}[]
     */
    protected function buildNavigation(Directory $tree): array
    {
        $nav = [];
        foreach ($tree->getEntries() as $node) {
            if ($node instanceof Content) {
                if ($node->isIndex()) {
                    continue;
                }

                $nav[] = [
                    'title' => $node->getTitle(),
                    'href' => '#' . $this->getPageUrl($node),
                ];
            } elseif ($node instanceof Directory) {
                if (!$node->hasContent()) {
                    continue;
                }

                $pageIndex = ($index = $node->getIndexPage()) ? $index : $node->getFirstPage();

                $nav[] = [
                    'title' => $node->getTitle(),
                    'href' => '#' . $this->getPageUrl($pageIndex),
                    'children' => $this->buildNavigation($node),
                ];
            }
        }

        return $nav;
    }

    private function renderNavigation($entries): string
    {
        $nav = '';
        foreach ($entries as $entry) {
            if (array_key_exists('children', $entry)) {
                if (array_key_exists('href', $entry)) {
                    $link = '<a href="' . $entry['href'] . '" class="Nav__item__link--nopage">' . $entry['title'] . '</a>';
                } else {
                    $link = '<a href="#" class="Nav__item__link Nav__item__link--nopage">' . $entry['title'] . '</a>';
                }

                $link .= $this->renderNavigation($entry['children']);
            } else {
                $link = '<a href="' . $entry['href'] . '">' . $entry['title'] . '</a>';
            }

            $nav .= "<li>$link</li>";
        }

        return "<ul>$nav</ul>";
    }

    protected function generateTOC(): string
    {
        return '<h1>Table of Contents</h1>' .
        $this->renderNavigation($this->buildNavigation($this->tree)) .
        '</div><div class="PageBreak">&nbsp;</div>';
    }

    protected function generateCover(): string
    {
        return '<div>' .
        "<h1 style='font-size:40pt; margin-bottom:0;'>{$this->config->getTitle()}</h1>" .
        "<p><strong>{$this->config->getTagline()}</strong> by {$this->config->getAuthor()}</p>" .
        '</div><div class="PageBreak">&nbsp;</div>';
    }

    protected function generatePages(): string
    {
        $content = '';
        foreach ($this->pages as $page) {
            $content .= '<a id="' . $this->getPageUrl($page['page']) . '"></a>';
            $content .= '<h1>' . $page['page']->getTitle() . '</h1>';
            $content .= '<section class="s-content">' . $page['content'] . '</section>';
            $content .= '<div class="PageBreak">&nbsp;</div>';
        }

        return $content;
    }

    public function addPage($page, $content): void
    {
        $this->pages[] = ['page' => $page, 'content' => $content];
    }

    public function generateHead(): string
    {
        $head = [
            "<title>{$this->config->getTitle()}</title>",
            "<meta name='description' content='{$this->config->getTagline()}' />",
            "<meta name='author' content='{$this->config->getAuthor()}'>",
            "<meta charset='UTF-8'>",
            $this->getStyles(),
        ];

        return '<head>' . implode('', $head) . '</head>';
    }

    public function generateBody(): string
    {
        return '<body>' . $this->generateCover() . $this->generateTOC() . $this->generatePages() . '</body>';
    }

    public function generate(): string
    {
        return '<!DOCTYPE html><html>' . $this->generateHead() . $this->generateBody() . '</html>';
    }
}
