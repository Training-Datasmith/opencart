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
     * Renders a `.tpl` template file by compiling it and executing it in an isolated scope.
     *
     * All keys in $data are extracted into local variables before the compiled template
     * is included. This is the standard OpenCart template rendering pattern — the
     * `extract()` call is intentional and required for template variable injection.
     *
     * @param string               $filename Template path relative to the configured directory
     *                                       (e.g. 'checkout/cart_list' — no .tpl suffix)
     * @param array<string, mixed> $data     Template variables; each key becomes a local variable
     *                                       in the template scope
     * @param string               $code     Pre-compiled template code; if non-empty, the file
     *                                       is not loaded from disk
     *
     * @return string The rendered template output as a string
     *
     * @throws \Exception If the template file does not exist on disk
     *
     * @warning $data keys become local variables via extract(). Avoid passing untrusted
     *          keys that could shadow built-in PHP variables or the $data/$code/$filename
     *          variables already in scope.
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