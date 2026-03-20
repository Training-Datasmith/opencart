<?php

declare (strict_types=1);
namespace Opencart\Admin\Controller\Event;

/**
 * Class Option
 *
 * @package Opencart\Admin\Controller\Event
 */
class Option extends \Opencart\System\Engine\Controller
{
    /*
     * Edit Option
     *
     * Adds task to generate new product data.
     *
     * Called using admin/model/catalog/option.editOption/after
     *
     * @param string                $route
     * @param array<string, string> $args
     * @param array<string, string> $output
     *
     * @return void
     */
    public function edit_option(string &$route, array &$args, &$output): void
    {
        $this->load->model('catalog/product');
        $results = $this->model_catalog_product->get_products_by_option_id($args[0]);
        $this->load->model('setting/task');
        foreach ($results as $result) {
            $task_data = ['code' => 'product.info.' . $result['product_id'], 'action' => 'task/catalog/product.info', 'args' => ['product_id' => $result['product_id']]];
            $this->model_setting_task->add_task($task_data);
        }
    }
    /*
     * Delete Option
     *
     * Adds task to generate new product data.
     *
     * Called using admin/model/catalog/option.deleteOption/after
     *
     * @param string                $route
     * @param array<string, string> $args
     * @param array<string, string> $output
     *
     * @return void
     */
    public function delete_option(string &$route, array &$args, &$output): void
    {
        $this->load->model('catalog/product');
        $results = $this->model_catalog_product->get_products_by_option_id($args[0]);
        $this->load->model('setting/task');
        foreach ($results as $result) {
            $task_data = ['code' => 'product.info.' . $result['product_id'], 'action' => 'task/catalog/product.info', 'args' => ['product_id' => $result['product_id']]];
            $this->model_setting_task->add_task($task_data);
        }
    }
}