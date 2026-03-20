<?php

declare (strict_types=1);
namespace Opencart\Admin\Controller\Task\Report;

/**
 * Class Stock
 *
 * @package Opencart\Admin\Controller\Report
 */
class Stock extends \Opencart\System\Engine\Controller
{
    /**
     * Index
     */
    public function index(array $args = []): array
    {
        $this->load->language('task/report/stock');
        $this->load->model('catalog/product');
        $this->load->model('report/statistics');
        $this->model_report_statistics->edit_value('product', $this->model_catalog_product->get_total_products(['filter_quantity_to' => 0]));
        return ['success' => $this->language->get('text_success')];
    }
}