<?php

declare(strict_types=1);

namespace Opencart\Admin\Controller\Event;

/**
 * Class Debug
 *
 * @package Opencart\Admin\Controller\Event
 */
class Debug extends \Opencart\System\Engine\Controller
{
    /**
     * Before
     *
     * @param array<int, mixed> $args
     *
     */
    public function before(string &$route, array &$args): void
    {
        $this->session->data['debug'][$route] = microtime();
    }

    /**
     * After
     *
     * @param array<int, mixed> $args
     * @param mixed             $output
     *
     */
    public function after(string $route, array &$args, &$output): void
    {
        if (isset($this->session->data['debug'][$route])) {
            $log_data = [
                'route' => $route,
                'time'  => microtime() - $this->session->data['debug'][$route],
            ];

            $this->log->write($route);
        }
    }
}
