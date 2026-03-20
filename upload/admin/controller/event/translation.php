<?php

declare (strict_types=1);
namespace Opencart\Admin\Controller\Event;

/**
 * Class Translation
 *
 * @package Opencart\Admin\Controller\Event
 */
class Translation extends \Opencart\System\Engine\Controller
{
    /**
     * Add Translation
     *
     * Adds task to generate new translation data
     *
     * Called using admin/model/design/translation.addTranslation/after
     *
     * @param array<string, string> $args
     *
     */
    public function add_translation(string &$route, array &$args, string &$output): void
    {
        $task_data = ['code' => 'translation.info.' . $output, 'action' => 'task/catalog/translation.info', 'args' => ['translation_id' => $output]];
        $this->load->model('setting/task');
        $this->model_setting_task->add_task($task_data);
    }
    /**
     * Edit Translation
     *
     * Adds task to generate new translation data
     *
     * Called using admin/model/design/translation.editTranslation/after
     *
     * @param array<string, string> $args
     *
     */
    public function edit_translation(string &$route, array &$args, &$output): void
    {
        $task_data = ['code' => 'translation.info.' . $args[0], 'action' => 'task/catalog/translation.info', 'args' => ['translation_id' => $args[0]]];
        $this->load->model('setting/task');
        $this->model_setting_task->add_task($task_data);
    }
    /**
     * Delete Translation
     *
     * Adds task to generate new translation data
     *
     * Called using admin/model/design/translation.deleteTranslation/after
     *
     * @param array<string, string> $args
     *
     */
    public function delete_translation(string &$route, array &$args, &$output): void
    {
        $task_data = ['code' => 'translation.delete.' . $args[0], 'action' => 'task/catalog/translation.delete', 'args' => ['translation_id' => $args[0]]];
        $this->load->model('setting/task');
        $this->model_setting_task->add_task($task_data);
    }
}