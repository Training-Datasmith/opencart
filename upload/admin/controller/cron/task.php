<?php

declare(strict_types=1);

namespace Opencart\Admin\Controller\Cron;

/**
 * Class Task
 *
 * @package Opencart\Catalog\Controller\Cron
 */
class Task extends \Opencart\System\Engine\Controller
{
    /**
     * Index
     *
     *
     */
    public function index(int $cron_id, string $code, string $cycle, string $date_added, string $date_modified): void
    {
        $this->load->model('setting/task');

        $task_total = $this->model_setting_task->getTotalTasks(['filter_status' => 'processing']);

        if (!$task_total) {
            if (str_starts_with(strtoupper(php_uname()), 'WIN')) {
                pclose(popen('start /B php ' . DIR_APPLICATION . 'index.php start', 'r'));
            } else {
                exec(DIR_APPLICATION . 'index.php start > /dev/null 2>&1 &');
            }
        }
    }
}
