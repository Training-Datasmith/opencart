<?php

declare(strict_types=1);

namespace Opencart\Catalog\Controller\Startup;

/**
 * Class Error
 *
 * @package Opencart\Catalog\Controller\Startup
 */
class Error extends \Opencart\System\Engine\Controller
{
    /**
     * Index
     */
    public function index(): void
    {
        set_error_handler([$this, 'error']);
        set_exception_handler([$this, 'exception']);
    }

    /**
     * Error
     *
     *
     * @throws \ErrorException
     */
    public function error(int $code, string $message, string $file, int $line): bool
    {
        // error suppressed with @
        if (!(error_reporting() & $code)) {
            return false;
        }

        throw new \ErrorException($message, 0, $code, $file, $line);
    }

    /**
     * Exception
     *
     * @param \Throwable $e
     */
    public function exception(object $e): void
    {
        $message = $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine();

        if ($this->config->get('config_error_log')) {
            $this->log->write($message);
        }

        if ($this->config->get('config_error_display')) {
            echo $message;
        } else {
            header('Location: ' . $this->config->get('error_page'));
            exit();
        }
    }
}
