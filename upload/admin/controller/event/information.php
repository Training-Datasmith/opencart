<?php

declare (strict_types=1);
namespace Opencart\Admin\Controller\Event;

/**
 * Class Information
 *
 * @package Opencart\Admin\Controller\Event
 */
class Information extends \Opencart\System\Engine\Controller
{
    /**
     * Add Information
     *
     * Adds task to generate new information data.
     *
     * Called using model/catalog/information/addInformation/after
     *
     * @param array<int, mixed> $args
     * @param mixed             $output
     *
     */
    public function add_information(string &$route, array &$args, string &$output): void
    {
        $task_data = ['code' => 'information.list', 'action' => 'task/catalog/information.list', 'args' => []];
        $this->load->model('setting/task');
        $this->model_setting_task->add_task($task_data);
        $task_data = ['code' => 'information.info.' . $output, 'action' => 'task/catalog/information.info', 'args' => ['information_id' => $output]];
        $this->model_setting_task->add_task($task_data);
    }
    /**
     * Edit Information
     *
     * Adds task to generate new information data.
     *
     * Called using model/catalog/information/addInformation/after
     *
     * @param array<int, mixed> $args
     * @param mixed             $output
     *
     */
    public function edit_information(string &$route, array &$args, &$output): void
    {
        $task_data = ['code' => 'information.list', 'action' => 'task/catalog/information.list', 'args' => []];
        $this->load->model('setting/task');
        $this->model_setting_task->add_task($task_data);
        $task_data = ['code' => 'information.info.' . $args[0], 'action' => 'task/catalog/information.info', 'args' => ['information_id' => $args[0]]];
        $this->model_setting_task->add_task($task_data);
    }
    /**
     * Delete Information
     *
     * Adds task to generate new information data.
     *
     * Called using model/catalog/information/deleteInformation/after
     *
     * @param array<int, mixed> $args
     * @param mixed             $output
     *
     */
    public function delete_information(string &$route, array &$args, &$output): void
    {
        $task_data = ['code' => 'information.list', 'action' => 'task/catalog/information.list', 'args' => []];
        $this->load->model('setting/task');
        $this->model_setting_task->add_task($task_data);
        $task_data = ['code' => 'information.delete.' . $args[0], 'action' => 'task/catalog/information.delete', 'args' => ['information_id' => $args[0]]];
        $this->model_setting_task->add_task($task_data);
    }
}