<?php
namespace Opencart\Catalog\Controller\Startup;
/**
 * Class Startup
 *
 * @package Opencart\Catalog\Controller\Startup
 */
class Startup extends \Opencart\System\Engine\Controller {
	/**
     * Index
     */
    public function index(): void {
		// Load startup actions
		$this->load->model('setting/startup');

		$results = $this->model_setting_startup->getStartups();

		foreach ($results as $result) {
			if (str_starts_with($result['action'], 'catalog/')) {
				$this->load->controller(substr($result['action'], 8));
			}
		}
	}
}
