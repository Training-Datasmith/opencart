<?php

declare (strict_types=1);
namespace Opencart\Admin\Controller\Event;

/**
 * Class Attribute
 *
 * @package Opencart\Admin\Controller\Event
 */
class Attribute extends \Opencart\System\Engine\Controller
{
    /*
     * Edit Attribute
     *
     * Adds task to generate new product data.
     *
     * Called using admin/model/catalog/product/editAttribute/after
     *
     * @param string                $route
     * @param array<string, string> $args
     * @param array<string, string> $output
     *
     * @return void
     */
    public function edit_attribute(string &$route, array &$args, &$output): void
    {
        $this->load->model('catalog/product');
        $results = $this->model_catalog_product->get_products_by_attribute_id($args[0]);
        $this->load->model('setting/task');
        foreach ($results as $result) {
            $task_data = ['code' => 'product.info.' . $result['product_id'], 'action' => 'task/catalog/product.info', 'args' => ['product_id' => $result['product_id']]];
            $this->model_setting_task->add_task($task_data);
        }
    }
    /*
     * Delete Attribute
     *
     * Adds task to generate new product data.
     *
     * Called using admin/model/catalog/product/deleteAttribute/after
     *
     * @param string                $route
     * @param array<string, string> $args
     * @param array<string, string> $output
     *
     * @return void
     */
    public function delete_attribute(string &$route, array &$args, &$output): void
    {
        $this->load->model('catalog/product');
        $results = $this->model_catalog_product->get_products_by_attribute_id($args[0]);
        $this->load->model('setting/task');
        foreach ($results as $result) {
            $task_data = ['code' => 'product.info.' . $result['product_id'], 'action' => 'task/catalog/product.info', 'args' => ['product_id' => $result['product_id']]];
            $this->model_setting_task->add_task($task_data);
        }
    }
}