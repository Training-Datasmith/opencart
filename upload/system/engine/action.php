<?php

declare (strict_types=1);
/**
 * @package     OpenCart
 *
 * @author      Daniel Kerr
 * @copyright   Copyright (c) 2005 - 2022, OpenCart, Ltd. (https://www.opencart.com/)
 * @license     https://opensource.org/licenses/GPL-3.0
 *
 * @see        https://www.opencart.com
 */
namespace Opencart\System\Engine;

/**
 * Class Action
 *
 * Allows the stored action to be passed around and be executed by the framework and events.
 *
 * @package Opencart\System\Engine
 */
class Action
{
    private string $route;
    private string $controller;
    private string $method;
    /**
     * Constructor
     */
    public function __construct(string $route)
    {
        $this->route = preg_replace('/[^a-zA-Z0-9_|\/\.]/', '', $route);
        $pos = strrpos($route, '.');
        if ($pos !== false) {
            $this->controller = substr($route, 0, $pos);
            $this->method = substr($route, $pos + 1);
        } else {
            $this->controller = $route;
            $this->method = 'index';
        }
    }
    /**
     * Get Id
     */
    public function get_id(): string
    {
        return $this->route;
    }
    /**
     * Execute
     *
     * @param array<mixed>                     $args
     * @return mixed
     */
    public function execute(\Opencart\System\Engine\Registry $registry, array &$args = [])
    {
        // Stop any magical methods being called
        if (str_starts_with($this->method, '__')) {
            return new \Exception('Error: Calls to magic methods are not allowed!');
        }
        // Create a new key to store the model object
        $key = 'fallback_controller_' . str_replace('/', '_', $this->controller);
        if (!$registry->has($key)) {
            $object = $registry->get('factory')->controller($this->controller);
        } else {
            $object = $registry->get($key);
        }
        if ($object instanceof \Opencart\System\Engine\Controller) {
            $registry->set($key, $object);
        } else {
            // If action cannot be executed, we return an error object.
            return new \Exception('Error: Could not load controller ' . $this->route . '!');
        }
        $callable = [$object, $this->method];
        if (is_callable($callable)) {
            return $callable(...$args);
        }
        return new \Exception('Error: Could not call controller ' . $this->route . '!');
    }
}