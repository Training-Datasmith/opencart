<?php
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
class Controller {
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
    public function __get(string $key): object {
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
    public function __set(string $key, object $value): void {
		$this->registry->set($key, $value);
	}
}
