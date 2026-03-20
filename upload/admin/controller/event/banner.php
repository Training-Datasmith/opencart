<?php

declare (strict_types=1);
namespace Opencart\Admin\Controller\Event;

/**
 * Class Banner
 *
 * @package Opencart\Admin\Controller\Event
 */
class Banner extends \Opencart\System\Engine\Controller
{
    /*
     * Add Banner
     *
     * Adds task to generate new banner data.
     *
     * Called using admin/model/deign/banner/addBanner/after
     *
     * @param string                $route
     * @param array<string, string> $args
     * @param array<string, string> $output
     *
     * @return void
     */
    public function add_banner(string &$route, array &$args, string &$output): void
    {
        $task_data = ['code' => 'banner.info.' . $output, 'action' => 'task/catalog/banner', 'args' => ['banner_id' => $output]];
        $this->load->model('setting/task');
        $this->model_setting_task->add_task($task_data);
    }
    /*
     * Edit Banner
     *
     * Adds task to generate new banner data.
     *
     * Called using admin/model/deign/banner/addBanner/after
     *
     * @param string                $route
     * @param array<string, string> $args
     * @param array<string, string> $output
     *
     * @return void
     */
    public function edit_banner(string &$route, array &$args, &$output): void
    {
        $task_data = ['code' => 'banner.info.' . $args[0], 'action' => 'task/catalog/banner.info', 'args' => ['banner_id' => $args[0]]];
        $this->load->model('setting/task');
        $this->model_setting_task->add_task($task_data);
    }
    /*
     * Delete Banner
     *
     * Adds task to generate new banner data.
     *
     * Called using admin/model/deign/banner/addBanner/after
     *
     * @param string                $route
     * @param array<string, string> $args
     * @param array<string, string> $output
     *
     * @return void
     */
    public function delete_banner(string &$route, array &$args, &$output): void
    {
        $task_data = ['code' => 'banner.delete.' . $args[0], 'action' => 'task/catalog/banner.delete', 'args' => ['banner_id' => $args[0]]];
        $this->load->model('setting/task');
        $this->model_setting_task->add_task($task_data);
    }
}