<?php

declare (strict_types=1);
namespace Opencart\System\Library\Template;

/**
 * Class Template
 *
 * @package Opencart\System\Library\Template
 */
class Template
{
    protected string $directory = '';
    /**
     * @var array<string, string>
     */
    protected array $path = [];
    /**
     * Add Path
     *
     *
     */
    public function add_path(string $namespace, string $directory = ''): void
    {
        if (!$directory) {
            $this->directory = $namespace;
        } else {
            $this->path[$namespace] = $directory;
        }
    }
    /**
     * Render
     *
     * @param array<string, mixed> $data
     *
     */
    public function render(string $filename, array $data = [], string $code = ''): string
    {
        if (!$code) {
            $file = $this->directory . $filename . '.tpl';
            $namespace = '';
            $parts = explode('/', $filename);
            foreach ($parts as $part) {
                if (!$namespace) {
                    $namespace .= $part;
                } else {
                    $namespace .= '/' . $part;
                }
                if (isset($this->path[$namespace])) {
                    $file = $this->path[$namespace] . substr($filename, strlen($namespace) + 1) . '.tpl';
                }
            }
            if (!is_file($file)) {
                throw new \Exception('Error: Could not load template ' . $filename . '!');
            }
            $code = file_get_contents($file);
        }
        if ($code) {
            ob_start();
            extract($data);
            include $this->compile($filename, $code);
            return ob_get_clean();
        }
        return '';
    }
    /**
     * Compile
     *
     *
     */
    protected function compile(string $filename, string $code): string
    {
        $file = DIR_CACHE . 'template/' . hash('md5', $filename . $code) . '.php';
        if (!is_file($file)) {
            file_put_contents($file, $code, LOCK_EX);
        }
        return $file;
    }
}