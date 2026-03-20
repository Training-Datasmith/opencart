<?php

declare (strict_types=1);
namespace Opencart\Admin\Controller\Event;

/**
 * Class Manufacturer
 *
 * @package Opencart\Admin\Controller\Event
 */
class Manufacturer extends \Opencart\System\Engine\Controller
{
    /**
     * Add Manufacturer
     *
     * Adds task to generate new manufacturer data.
     *
     * Called using model/catalog/manufacturer/addManufacturer/after
     *
     * @param array<int, mixed> $args
     * @param mixed             $output
     *
     */
    public function add_manufacturer(string &$route, array &$args, string &$output): void
    {
        // List
        $task_data = ['code' => 'manufacturer.list', 'action' => 'task/catalog/manufacturer.list', 'args' => []];
        $this->load->model('setting/task');
        $this->model_setting_task->add_task($task_data);
        // Info
        $task_data = ['code' => 'manufacturer.info.' . $output, 'action' => 'task/catalog/manufacturer.info', 'args' => ['manufacturer_id' => $output]];
        $this->model_setting_task->add_task($task_data);
    }
    /**
     * Edit Manufacturer
     *
     * Adds task to generate new manufacturer data.
     *
     * Called using model/catalog/manufacturer/editManufacturer/after
     *
     * @param array<int, mixed> $args
     * @param mixed             $output
     *
     */
    public function edit_manufacturer(string &$route, array &$args, &$output): void
    {
        // List
        $task_data = ['code' => 'manufacturer.list', 'action' => 'task/catalog/manufacturer.list', 'args' => []];
        $this->load->model('setting/task');
        $this->model_setting_task->add_task($task_data);
        // Info
        $task_data = ['code' => 'manufacturer.info.' . $args[0], 'action' => 'task/catalog/manufacturer.info', 'args' => ['manufacturer_id' => $args[0]]];
        $this->model_setting_task->add_task($task_data);
        // Products
        $this->load->model('catalog/product');
        $results = $this->model_catalog_product->get_products_by_manufacturer_id($args[0]);
        foreach ($results as $result) {
            $task_data = ['code' => 'product.info.' . $result['product_id'], 'action' => 'task/catalog/product.info', 'args' => ['product_id' => $result['product_id']]];
            $this->model_setting_task->add_task($task_data);
        }
    }
    /**
     * Delete Manufacturer
     *
     * Adds task to generate new manufacturer data.
     *
     * Called using model/catalog/manufacturer/editManufacturer/after
     *
     * @param array<int, mixed> $args
     * @param mixed             $output
     *
     */
    public function delete_manufacturer(string &$route, array &$args, &$output): void
    {
        // List
        $task_data = ['code' => 'manufacturer.list', 'action' => 'task/catalog/manufacturer.list', 'args' => []];
        $this->load->model('setting/task');
        $this->model_setting_task->add_task($task_data);
        // Delete
        $task_data = ['code' => 'manufacturer.delete.' . $args[0], 'action' => 'task/catalog/manufacturer.delete', 'args' => ['manufacturer_id' => $args[0]]];
        $this->load->model('setting/task');
        $this->model_setting_task->add_task($task_data);
        // Products
        $this->load->model('catalog/product');
        $results = $this->model_catalog_product->get_products_by_manufacturer_id($args[0]);
        foreach ($results as $result) {
            $task_data = ['code' => 'product.info.' . $result['product_id'], 'action' => 'task/catalog/product.info', 'args' => ['product_id' => $result['product_id']]];
            $this->model_setting_task->add_task($task_data);
        }
    }
}