<?php
namespace Opencart\Catalog\Controller\Event;
/**
 * Class Debug
 *
 * @package Opencart\Catalog\Controller\Event
 */
class Debug extends \Opencart\System\Engine\Controller {
	/**
     * Before
     *
     * @param array<int, mixed> $args
     *
     */
    public function before(string &$route, array &$args): void {
		// add the route you want to test
		$this->session->data['debug'][$route] = microtime(true);
	}

	/**
     * After
     *
     * @param array<int, mixed> $args
     * @param mixed             $output
     *
     */
    public function after(string &$route, array &$args, &$output): void {
		// add the route you want to test
		if (isset($this->session->data['debug'][$route])) {
			$log_data = [
				'route' => $route,
				'time'  => microtime(true) - $this->session->data['debug'][$route]
			];

			$this->log->write($log_data);

			unset($this->session->data['debug']);
		}
	}
}
