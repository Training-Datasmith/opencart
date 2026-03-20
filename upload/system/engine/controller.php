<?php

declare(strict_types=1);
/**
 * @package      OpenCart
 *
 * @author       Daniel Kerr
 * @copyright    Copyright (c) 2005 - 2022, OpenCart, Ltd. (https://www.opencart.com/)
 * @license      https://opensource.org/licenses/GPL-3.0
 *
 * @see         https://www.opencart.com
 */

namespace Opencart\System\Engine;

/**
 * Class Controller
 *
 * @mixin \Opencart\System\Engine\Registry
 */
class Controller
{
    /**
     * Injects the service registry used for lazy-loading all framework services.
     *
     * @param \Opencart\System\Engine\Registry $registry Central service registry containing
     *                                                    db, config, cart, session, request, etc.
     */
    public function __construct(protected \Opencart\System\Engine\Registry $registry)
    {
    }

    /**
     * Proxies property reads to the service registry.
     *
     * Allows controllers to access services as properties (e.g. `$this->cart`, `$this->db`).
     *
     * @param string $key Registry service name (e.g. 'cart', 'session', 'config', 'db')
     *
     * @return object The registered service instance
     *
     * @throws \Exception If the key has not been registered in the registry
     */
    public function __get(string $key): object
    {
        if (!$this->registry->has($key)) {
            throw new \Exception('Error: Could not call registry key ' . $key . '!');
        }

        return $this->registry->get($key);
    }

    /**
     * Proxies property writes to the service registry.
     *
     * Allows controllers to bind services dynamically (e.g. `$this->myService = $obj`).
     *
     * @param string $key   Registry key to register under
     * @param object $value Service instance to bind
     */
    public function __set(string $key, object $value): void
    {
        $this->registry->set($key, $value);
    }
}
