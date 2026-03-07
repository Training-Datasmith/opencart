<?php
namespace Opencart\Admin\Controller\Cron;
/**
 * Class Currency
 *
 * @package Opencart\Catalog\Controller\Cron
 */
class Currency extends \Opencart\System\Engine\Controller {
	/**
     * Index
     *
     *
     */
    public function index(int $cron_id, string $code, string $cycle, string $date_added, string $date_modified): void {
		if (!$this->config->get('config_currency_auto')) {
			return;
		}

		$task_data = [
			'code'   => 'currency',
			'action' => 'task/admin/currency.refresh',
			'args'   => []
		];

		$this->load->model('setting/task');

		$this->model_setting_task->addTask($task_data);
	}
}
