<?php

declare (strict_types=1);
namespace Opencart\Catalog\Controller\Event;

/**
 * Class Statistics
 *
 * @package Opencart\Catalog\Controller\Event
 */
class Statistics extends \Opencart\System\Engine\Controller
{
    /**
     * Add Review
     *
     * Trigger
     *
     * catalog/model/catalog/review.addReview/after
     *
     * @param array<int, mixed> $args
     * @param mixed             $output
     *
     */
    public function add_review(string &$route, array &$args, &$output): void
    {
        // Stats
        $this->load->model('report/statistics');
        $this->model_report_statistics->add_value('review', 1);
    }
    /**
     * Add Return
     *
     * Trigger
     *
     * catalog/model/account/returns.addReturn/after
     *
     * @param array<int, mixed> $args
     * @param mixed             $output
     *
     */
    public function add_return(string &$route, array &$args, &$output): void
    {
        // Stats
        $this->load->model('report/statistics');
        $this->model_report_statistics->add_value('returns', 1);
    }
    /**
     * Add History
     *
     * Trigger
     *
     * catalog/model/checkout/order.addHistory/before
     *
     * @param array<int, mixed> $args
     *
     */
    public function add_history(string &$route, array &$args): void
    {
        // Order
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->get_order($args[0]);
        if ($order_info) {
            // Stats
            $this->load->model('report/statistics');
            $old_status_id = $order_info['order_status_id'];
            $new_status_id = $args[1];
            $processing_status = (array) $this->config->get('config_processing_status');
            $complete_status = (array) $this->config->get('config_complete_status');
            $active_status = array_merge($processing_status, $complete_status);
            // If order status in complete or processing add value to sale total
            if (in_array($new_status_id, $active_status) && !in_array($old_status_id, $active_status)) {
                $this->model_report_statistics->add_value('order_sale', $order_info['total']);
            }
            // If order status not in complete or processing remove value to sale total
            if (!in_array($new_status_id, $active_status) && in_array($old_status_id, $active_status)) {
                $this->model_report_statistics->remove_value('order_sale', $order_info['total']);
            }
            // Add to processing status if new status is in the array
            if (in_array($new_status_id, $processing_status) && !in_array($old_status_id, $processing_status)) {
                $this->model_report_statistics->add_value('order_processing', 1);
            }
            // Remove from processing status if new status is not array and old status is
            if (!in_array($new_status_id, $processing_status) && in_array($old_status_id, $processing_status)) {
                $this->model_report_statistics->remove_value('order_processing', 1);
            }
            // Add to complete status if new status is not array
            if (in_array($new_status_id, $complete_status) && !in_array($old_status_id, $complete_status)) {
                $this->model_report_statistics->add_value('order_complete', 1);
            }
            // Remove from complete status if new status is not array
            if (!in_array($new_status_id, $complete_status) && in_array($old_status_id, $complete_status)) {
                $this->model_report_statistics->remove_value('order_complete', 1);
            }
        }
    }
}