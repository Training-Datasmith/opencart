<?php

declare (strict_types=1);
/**
 * @package		OpenCart
 *
 * @author		Daniel Kerr
 * @copyright	Copyright (c) 2005 - 2022, OpenCart, Ltd. (https://www.opencart.com/)
 * @license		https://opensource.org/licenses/GPL-3.0
 *
 * @see		https://www.opencart.com
 */
namespace Opencart\System\Library;

/**
 * Class Document
 */
class Document
{
    private string $title = '';
    private string $description = '';
    private string $keywords = '';
    /**
     * @var array<string, array<string, string>>
     */
    private array $links = [];
    /**
     * @var array<string, array<string, string>>
     */
    private array $styles = [];
    /**
     * @var array<string, array<string, array<string, string>>>
     */
    private array $scripts = [];
    /**
     * @var array<int, array<string, string>> Meta tags with their attributes
     */
    private array $metas = [];
    /**
     * Set Title
     *
     *
     */
    public function set_title(string $title): void
    {
        $this->title = $title;
    }
    /**
     * Get Title
     */
    public function get_title(): string
    {
        return $this->title;
    }
    /**
     * Set Description
     *
     *
     */
    public function set_description(string $description): void
    {
        $this->description = $description;
    }
    /**
     * Get Description
     */
    public function get_description(): string
    {
        return $this->description;
    }
    /**
     * Set Keywords
     */
    public function set_keywords(string $keywords): void
    {
        $this->keywords = $keywords;
    }
    /**
     * Get Keywords
     */
    public function get_keywords(): string
    {
        return $this->keywords;
    }
    /**
     * Add Link
     *
     *
     */
    public function add_link(string $href, string $rel): void
    {
        $this->links[$href] = ['href' => $href, 'rel' => $rel];
    }
    /**
     * Get Links
     *
     * @return array<string, array<string, string>>
     */
    public function get_links(): array
    {
        return $this->links;
    }
    /**
     * Add Style
     *
     *
     */
    public function add_style(string $href, string $rel = 'stylesheet', string $media = 'screen'): void
    {
        $this->styles[$href] = ['href' => $href, 'rel' => $rel, 'media' => $media];
    }
    /**
     * Get Styles
     *
     * @return array<string, array<string, string>>
     */
    public function get_styles(): array
    {
        return $this->styles;
    }
    /**
     * Add Script
     *
     * @param string $position
     *
     */
    public function add_script(string $href): void
    {
        $this->scripts[$href] = ['href' => $href];
    }
    /**
     * Get Scripts
     *
     * @param string $position
     *
     * @return array<string, array<string, string>>
     */
    public function get_scripts(): array
    {
        return $this->scripts;
    }
    /**
     * Add Meta
     *
     * Adds a meta tag with specified attributes to the document.
     *
     * @param array<string, string> $attributes Associative array of meta tag attributes
     *                                          Common attributes:
     *                                          - 'name' => 'description' (for standard meta tags)
     *                                          - 'property' => 'og:title' (for Open Graph)
     *                                          - 'content' => 'The content value'
     *                                          - 'media' => '(prefers-color-scheme: dark)' (for conditional meta tags)
     *
     *
     * @example
     * $this->document->addMeta(['name' => 'description', 'content' => 'Page description']);
     * $this->document->addMeta(['property' => 'og:title', 'content' => 'Page Title']);
     * $this->document->addMeta(['name' => 'theme-color', 'content' => '#000', 'media' => '(prefers-color-scheme: dark)']);
     */
    public function add_meta(array $attributes): void
    {
        $this->metas[] = $attributes;
    }
    /**
     * Get Metas
     *
     * @return array<int, array<string, string>>
     */
    public function get_metas(): array
    {
        return $this->metas;
    }
}