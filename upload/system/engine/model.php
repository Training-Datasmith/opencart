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

/**
 * Model class
 */

namespace Opencart\System\Engine;

/**
 * Class Model
 *
 * @mixin \Opencart\System\Engine\Registry
 */
class Model
{
    /**
     * Constructor
     */
    public function __construct(protected \Opencart\System\Engine\Registry $registry)
    {
    }

    /**
     * __get
     *
     *
     */
    public function __get(string $key): object
    {
        if (!$this->registry->has($key)) {
            throw new \Exception('Error: Could not call registry key ' . $key . '!');
        }

        return $this->registry->get($key);
    }

    /**
     * __set
     *
     *
     */
    public function __set(string $key, object $value): void
    {
        $this->registry->set($key, $value);
    }

    /**
     * __isset
     *
     * https://www.php.net/manual/en/language.oop5.overloading.php#object.set
     *
     *
     */
    public function __isset(string $key): bool
    {
        return $this->registry->has($key);
    }
}
