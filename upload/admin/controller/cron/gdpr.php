<?php
namespace Opencart\Admin\Controller\Cron;
/**
 * Class Gdpr
 *
 * @package Opencart\Catalog\Controller\Cron
 */
class Gdpr extends \Opencart\System\Engine\Controller {
	/**
     * Index
     *
     *
     */
    public function index(int $cron_id, string $code, string $cycle, string $date_added, string $date_modified): void {
		$task_data = [
			'code'   => 'gdpr',
			'action' => 'task/admin/gdpr',
			'args'   => []
		];

		$this->load->model('setting/task');

		$this->model_setting_task->addTask($task_data);
	}
}
