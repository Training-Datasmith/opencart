<?php

declare(strict_types=1);
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
 * Class Response
 *
 * Stores the response so the correct headers can go out before the response output is shown.
 */
class Response
{
    /**
     * @var array<int, string>
     */
    private array $headers = [];
    private int $level = 0;
    private string $output = '';

    /**
     * Constructor
     */
    public function addHeader(string $header): void
    {
        $this->headers[] = $header;
    }

    /**
     * Get Headers
     *
     * @return array<int, string>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Redirect
     *
     *
     */
    public function redirect(string $url, int $status = 302): void
    {
        header('Location: ' . str_replace(['&amp;', "\n", "\r"], ['&', '', ''], $url), true, $status);
        exit();
    }

    /**
     * Set Compression
     *
     *
     */
    public function setCompression(int $level): void
    {
        $this->level = $level;
    }

    /**
     * Set Output
     *
     *
     */
    public function setOutput(string $output): void
    {
        $this->output = $output;
    }

    /**
     * Get Output
     */
    public function getOutput(): string
    {
        return $this->output;
    }

    /**
     * Compress
     *
     *
     */
    private function compress(string $data, int $level = 0): string
    {
        if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && (str_contains($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip'))) {
            $encoding = 'gzip';
        }

        if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && (str_contains($_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip'))) {
            $encoding = 'x-gzip';
        }

        if (!isset($encoding) || ($level < -1 || $level > 9)) {
            return $data;
        }

        if (!extension_loaded('zlib') || ini_get('zlib.output_compression')) {
            return $data;
        }

        if (headers_sent()) {
            return $data;
        }

        if (connection_status()) {
            return $data;
        }

        $this->addHeader('Content-Encoding: ' . $encoding);

        return gzencode($data, $level);
    }

    /**
     * Output
     *
     * Displays the set HTML output
     */
    public function output(): void
    {
        if ($this->output) {
            $output = $this->level ? $this->compress($this->output, $this->level) : $this->output;

            if (!headers_sent()) {
                foreach ($this->headers as $header) {
                    header($header, true);
                }
            }

            echo $output;
        }
    }
}
