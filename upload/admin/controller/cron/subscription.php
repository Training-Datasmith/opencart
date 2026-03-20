<?php

declare (strict_types=1);
namespace Opencart\Admin\Controller\Cron;

/**
 * Class Subscription
 *
 * @package Opencart\Catalog\Controller\Cron
 */
class Subscription extends \Opencart\System\Engine\Controller
{
    /**
     * Index
     *
     *
     */
    public function index(int $cron_id, string $code, string $cycle, string $date_added, string $date_modified): void
    {
        $task_data = ['code' => 'subscription', 'action' => 'task/admin/subscription', 'args' => []];
        $this->load->model('setting/task');
        $this->model_setting_task->add_task($task_data);
    }
}