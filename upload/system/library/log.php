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
 * Class Log
 */
class Log
{
    private string $file;

    /**
     * Constructor
     */
    public function __construct(string $filename)
    {
        $this->file = DIR_LOGS . $filename;

        if (!is_file($this->file)) {
            $handle = fopen($this->file, 'w');

            fclose($handle);
        }
    }

    /**
     * Write
     *
     * @param mixed $message
     */
    public function write($message): void
    {
        file_put_contents($this->file, date('Y-m-d H:i:s') . ' - ' . print_r($message, true) . "\n", FILE_APPEND);
    }
}
