<?php

declare (strict_types=1);
namespace Opencart\Admin\Controller\Cron;

/**
 * Class Backup
 *
 * @package Opencart\Catalog\Controller\Cron
 */
class Backup extends \Opencart\System\Engine\Controller
{
    /**
     * Index
     *
     *
     */
    public function index(int $cron_id, string $code, string $cycle, string $date_added, string $date_modified): void
    {
        $task_data = ['code' => 'backup', 'action' => 'task/system/backup', 'args' => []];
        $this->load->model('setting/task');
        $this->model_setting_task->add_task($task_data);
    }
}