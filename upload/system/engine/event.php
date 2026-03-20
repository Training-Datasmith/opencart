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
namespace Opencart\System\Engine;

/**
 * Class Event
 *
 * https://github.com/opencart/opencart/wiki/Events-(script-notifications)-2.2.x.x
 */
class Event
{
    /**
     * @var array<int, array<string, mixed>>
     */
    protected array $data = [];
    /**
     * Constructor
     */
    public function __construct(protected \Opencart\System\Engine\Registry $registry)
    {
    }
    /**
     * Register
     *
     *
     */
    public function register(string $trigger, \Opencart\System\Engine\Action $action, int $priority = 0): void
    {
        $this->data[] = ['trigger' => $trigger, 'action' => $action, 'priority' => $priority];
        $sort_order = [];
        foreach ($this->data as $key => $value) {
            $sort_order[$key] = $value['priority'];
        }
        array_multisort($sort_order, SORT_ASC, $this->data);
    }
    /**
     * Trigger
     *
     * @param array<mixed> $args
     *
     */
    public function trigger(string $event, array $args = []): string
    {
        foreach ($this->data as $value) {
            if (preg_match('/^' . str_replace(['\*', '\?'], ['.*', '.'], preg_quote($value['trigger'], '/')) . '/', $event)) {
                $value['action']->execute($this->registry, $args);
            }
        }
        return '';
    }
    /**
     * Unregister
     *
     *
     */
    public function unregister(string $trigger, string $route): void
    {
        foreach ($this->data as $key => $value) {
            if ($trigger == $value['trigger'] && $value['action']->get_id() == $route) {
                unset($this->data[$key]);
            }
        }
    }
    /**
     * Clear
     *
     *
     */
    public function clear(string $trigger): void
    {
        foreach ($this->data as $key => $value) {
            if ($trigger == $value['trigger']) {
                unset($this->data[$key]);
            }
        }
    }
}