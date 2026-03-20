<?php

declare (strict_types=1);
namespace Opencart\Admin\Controller\Cron;

/**
 * Class Notification
 *
 * @package Opencart\Admin\Controller\Startup
 */
class Notification extends \Opencart\System\Engine\Controller
{
    /**
     * Index
     */
    public function index(): void
    {
        $task_data = ['code' => 'currency', 'action' => 'task/system/notification', 'args' => []];
        $this->load->model('setting/task');
        $this->model_setting_task->add_task($task_data);
    }
}