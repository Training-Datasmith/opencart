<?php

declare (strict_types=1);
namespace Opencart\Admin\Controller\Event;

/**
 * Class Customer Group
 *
 * @package Opencart\Admin\Controller\Event
 */
class Customer_Group extends \Opencart\System\Engine\Controller
{
    /**
     * Add Customer Group
     *
     * Adds task to generate new customer group data.
     *
     * Triggered using admin/model/customer/customer_group/addCustomerGroup/after
     *
     * @param array<string, string> $args
     *
     */
    public function add_customer_group(string &$route, array &$args, string &$output): void
    {
        $task_data = ['code' => 'customer_group.list', 'action' => 'task/catalog/customer_group.list', 'args' => []];
        $this->load->model('setting/task');
        $this->model_setting_task->add_task($task_data);
        $task_data = ['code' => 'customer_group.info.' . $output, 'action' => 'task/catalog/customer_group.info', 'args' => ['customer_group_id' => $output]];
        $this->load->model('setting/task');
        $this->model_setting_task->add_task($task_data);
    }
    /**
     * Edit Customer Group
     *
     * Adds task to generate new customer group data.
     *
     * Triggered using admin/model/customer/customer_group/editCustomerGroup/after
     *
     * @param array<string, string> $args
     *
     */
    public function edit_customer_group(string &$route, array &$args, &$output): void
    {
        $task_data = ['code' => 'customer_group.list', 'action' => 'task/catalog/customer_group.list', 'args' => []];
        $this->load->model('setting/task');
        $this->model_setting_task->add_task($task_data);
        $task_data = ['code' => 'customer_group.info.' . $args[0], 'action' => 'task/catalog/customer_group.info', 'args' => ['customer_group_id' => $args[0]]];
        $this->model_setting_task->add_task($task_data);
        // Admin
        /*
        $task_data = [
            'code'   => 'customer_group',
            'action' => 'task/admin/customer_group.list',
            'args'   => []
        ];
        
        $this->model_setting_task->addTask($task_data);
        
        $task_data = [
            'code'   => 'customer_group',
            'action' => 'task/admin/customer_group.info',
            'args'   => ['customer_group_id' => $args[0]]
        ];
        
        $this->model_setting_task->addTask($task_data);
        */
    }
    /**
     * Delete Customer Group
     *
     * Adds task to generate new customer group data.
     *
     * Triggered using admin/model/customer/customer_group/deleteCustomerGroup/after
     *
     * @param array<string, string> $args
     *
     */
    public function delete_customer_group(string &$route, array &$args, &$output): void
    {
        $task_data = ['code' => 'customer_group.list', 'action' => 'task/catalog/customer_group.list', 'args' => []];
        $this->load->model('setting/task');
        $this->model_setting_task->add_task($task_data);
        $task_data = ['code' => 'customer_group.delete.' . $args[0], 'action' => 'task/catalog/customer_group.delete', 'args' => ['customer_group_id' => $args[0]]];
        $this->model_setting_task->add_task($task_data);
        /*
        // Admin
        $task_data = [
            'code'   => 'country',
            'action' => 'task/admin/customer_group.delete',
            'args'   => ['country_id' => $args[0]]
        ];
        
        $this->model_setting_task->addTask($task_data);
        */
    }
}