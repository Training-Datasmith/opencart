<?php

declare (strict_types=1);
namespace Opencart\Admin\Controller\Task\Report;

/**
 * Class Sale
 *
 * @package Opencart\Admin\Controller\Task\Report
 */
class Sale extends \Opencart\System\Engine\Controller
{
    /**
     * Index
     *
     * Generate rating task list.
     */
    public function index(array $args = []): array
    {
        $this->load->language('task/catalog/sale');
        $this->load->model('setting/task');
        $limit = 10;
        $this->load->model('catalog/product');
        $product_total = $this->model_catalog_product->get_total_products();
        $page_total = ceil($product_total / $limit);
        for ($i = 1; $i <= $page_total; $i++) {
            $start = $i * $limit;
            $task_data = ['code' => 'sale', 'action' => 'task/report/sale.list', 'args' => ['start' => $start, 'limit' => $limit]];
            $this->model_setting_task->add_task($task_data);
        }
        return ['success' => $this->language->get('text_task')];
    }
    /**
     * List
     *
     * Calculates product sales.
     */
    public function list(array $args = []): array
    {
        $this->load->language('task/report/sale');
        $this->load->model('sale/order');
        $this->load->model('catalog/product');
        $results = $this->model_catalog_product->get_products($args);
        foreach ($results as $result) {
            $this->model_catalog_product->edit_sale($result['product_id'], $this->model_sale_order->get_total_sales(['filter_order_status' => implode(',', (array) $this->config->get('config_complete_status'))]));
        }
        $product_total = $this->model_catalog_product->get_total_products();
        return ['success' => sprintf($this->language->get('text_list'), $args['start'], $args['start'] > $product_total - $args['limit'] ? $product_total : $args['start'] + $args['limit'])];
    }
}