<?php

declare (strict_types=1);
namespace Opencart\Admin\Controller\Event;

/**
 * Class Custom Field
 *
 * @package Opencart\Admin\Controller\Event
 */
class Custom_Field extends \Opencart\System\Engine\Controller
{
    /**
     * Add Custom Field
     *
     * Adds task to generate new customer group data with the updated customer fields.
     *
     * Called using model/customer/custom_field/addCustomField/after
     *
     * @param array<int, mixed> $args
     * @param mixed             $output
     *
     */
    public function add_custom_field(string &$route, array &$args, &$output): void
    {
        $this->load->model('setting/task');
        $this->load->model('setting/custom_field');
        $results = $this->model_setting_custom_field->get_customer_groups($output);
        foreach ($results as $result) {
            $task_data = ['code' => 'customer_group.info', 'action' => 'task/catalog/customer_group', 'args' => ['customer_group_id' => $result['customer_group_id']]];
            $this->model_setting_task->add_task($task_data);
        }
    }
    /**
     * Edit Custom Field
     *
     * Adds task to generate new customer group data with the updated customer fields.
     *
     * Called using model/customer/custom_field/editCustomField/after
     *
     * @param array<int, mixed> $args
     * @param mixed             $output
     *
     */
    public function edit_custom_field(string &$route, array &$args, &$output): void
    {
        $this->load->model('setting/task');
        $this->load->model('setting/custom_field');
        $results = $this->model_setting_custom_field->get_customer_groups($output);
        foreach ($results as $result) {
            $task_data = ['code' => 'customer_group', 'action' => 'task/catalog/customer_group', 'args' => []];
            $this->model_setting_task->add_task($task_data);
        }
    }
    /**
     * Delete Custom Field
     *
     * Adds task to generate new customer group data with the updated customer fields.
     *
     * Called using model/customer/custom_field/deleteCustomField/after
     *
     * @param array<int, mixed> $args
     * @param mixed             $output
     *
     */
    public function delete_custom_field(string &$route, array &$args, &$output): void
    {
        $this->load->model('setting/task');
        $this->load->model('setting/custom_field');
        $results = $this->model_setting_custom_field->get_customer_groups($args[0]['custom_field_id']);
        foreach ($results as $result) {
            $task_data = ['code' => 'customer_group', 'action' => 'task/catalog/customer_group', 'args' => []];
            $this->model_setting_task->add_task($task_data);
        }
    }
}