<?php

declare (strict_types=1);
namespace Opencart\Catalog\Controller\Event;

/**
 * Class Activity
 *
 * @package Opencart\Catalog\Controller\Event
 */
class Activity extends \Opencart\System\Engine\Controller
{
    /**
     * Add Customer
     *
     * Trigger
     *
     * catalog/model/account/customer/addCustomer/after
     *
     * @param array<int, mixed> $args
     * @param mixed             $output
     *
     */
    public function add_customer(string &$route, array &$args, &$output): void
    {
        // Activity
        if ($this->config->get('config_customer_activity')) {
            $this->load->model('account/activity');
            $activity_data = ['customer_id' => $output, 'name' => $args[0]['firstname'] . ' ' . $args[0]['lastname']];
            $this->model_account_activity->add_activity('register', $activity_data);
        }
    }
    /**
     * Edit Customer
     *
     * Trigger
     *
     * catalog/model/account/customer/editCustomer/after
     *
     * @param array<int, mixed> $args
     * @param mixed             $output
     *
     */
    public function edit_customer(string &$route, array &$args, &$output): void
    {
        // Activity
        if ($this->config->get('config_customer_activity')) {
            $this->load->model('account/activity');
            $activity_data = ['customer_id' => $this->customer->get_id(), 'name' => $this->customer->get_first_name() . ' ' . $this->customer->get_last_name()];
            $this->model_account_activity->add_activity('edit', $activity_data);
        }
    }
    /**
     * Edit Password
     *
     * Trigger
     *
     * catalog/model/account/customer/editPassword/after
     *
     * @param array<int, mixed> $args
     * @param mixed             $output
     *
     */
    public function edit_password(string &$route, array &$args, &$output): void
    {
        // Activity
        if ($this->config->get('config_customer_activity')) {
            $this->load->model('account/activity');
            if ($this->customer->is_logged()) {
                $activity_data = ['customer_id' => $this->customer->get_id(), 'name' => $this->customer->get_first_name() . ' ' . $this->customer->get_last_name()];
                $this->model_account_activity->add_activity('password', $activity_data);
            } else {
                $customer_info = $this->model_account_customer->get_customer_by_email($args[0]);
                if ($customer_info) {
                    $activity_data = ['customer_id' => $customer_info['customer_id'], 'name' => $customer_info['firstname'] . ' ' . $customer_info['lastname']];
                    $this->model_account_activity->add_activity('reset', $activity_data);
                }
            }
        }
    }
    /**
     * Login
     *
     * Trigger
     *
     * catalog/model/account/customer/deleteLoginAttempts/after
     *
     * @param array<int, mixed> $args
     * @param mixed             $output
     *
     */
    public function login(string &$route, array &$args, &$output): void
    {
        if (isset($this->request->get['route']) && ($this->request->get['route'] == 'account/login' || $this->request->get['route'] == 'checkout/login.save') && $this->config->get('config_customer_activity')) {
            $customer_info = $this->model_account_customer->get_customer_by_email($args[0]);
            if ($customer_info) {
                $this->load->model('account/activity');
                $activity_data = ['customer_id' => $customer_info['customer_id'], 'name' => $customer_info['firstname'] . ' ' . $customer_info['lastname']];
                $this->model_account_activity->add_activity('login', $activity_data);
            }
        }
    }
    /**
     * Forgotten
     *
     * Trigger
     *
     * catalog/model/account/customer/addToken/after
     *
     * @param array<int, mixed> $args
     * @param mixed             $output
     *
     */
    public function forgotten(string &$route, array &$args, &$output): void
    {
        // Customer
        if (isset($this->request->get['route']) && $this->request->get['route'] == 'account/forgotten' && $this->config->get('config_customer_activity')) {
            $this->load->model('account/customer');
            $customer_info = $this->model_account_customer->get_customer_by_email($args[0]);
            if ($customer_info) {
                $this->load->model('account/activity');
                $activity_data = ['customer_id' => $customer_info['customer_id'], 'name' => $customer_info['firstname'] . ' ' . $customer_info['lastname']];
                $this->model_account_activity->add_activity('forgotten', $activity_data);
            }
        }
    }
    /**
     * Add Transaction
     *
     * Trigger
     *
     * catalog/model/account/customer/addTransaction/after
     *
     * @param array<int, mixed> $args
     * @param mixed             $output
     *
     */
    public function add_transaction(string &$route, array &$args, &$output): void
    {
        // Customer
        if ($this->config->get('config_customer_activity')) {
            $this->load->model('account/customer');
            $customer_info = $this->model_account_customer->get_customer($args[0]);
            if ($customer_info) {
                $this->load->model('account/activity');
                $activity_data = ['customer_id' => $customer_info['customer_id'], 'name' => $customer_info['firstname'] . ' ' . $customer_info['lastname'], 'order_id' => $args[3]];
                $this->model_account_activity->add_activity('transaction', $activity_data);
            }
        }
    }
    /**
     * Add Affiliate
     *
     * Trigger
     *
     * catalog/model/account/affiliate/addAffiliate/after
     *
     * @param array<int, mixed> $args
     * @param mixed             $output
     *
     */
    public function add_affiliate(string &$route, array &$args, &$output): void
    {
        // Activity
        if ($this->config->get('config_customer_activity')) {
            $this->load->model('account/activity');
            $activity_data = ['customer_id' => $this->customer->get_id(), 'name' => $this->customer->get_first_name() . ' ' . $this->customer->get_last_name()];
            $this->model_account_activity->add_activity('affiliate_add', $activity_data);
        }
    }
    /**
     * Edit Affiliate
     *
     * Trigger
     *
     * catalog/model/account/affiliate/editAffiliate/after
     *
     * @param array<int, mixed> $args
     * @param mixed             $output
     *
     */
    public function edit_affiliate(string &$route, array &$args, &$output): void
    {
        // Activity
        if ($this->config->get('config_customer_activity')) {
            $this->load->model('account/activity');
            $activity_data = ['customer_id' => $this->customer->get_id(), 'name' => $this->customer->get_first_name() . ' ' . $this->customer->get_last_name()];
            $this->model_account_activity->add_activity('affiliate_edit', $activity_data);
        }
    }
    /**
     * Add Address
     *
     * Trigger
     *
     * catalog/model/account/address/addAddress/after
     *
     * @param array<int, mixed> $args
     * @param mixed             $output
     *
     */
    public function add_address(string &$route, array &$args, &$output): void
    {
        // Activity
        if ($this->config->get('config_customer_activity')) {
            $this->load->model('account/activity');
            $activity_data = ['customer_id' => $this->customer->get_id(), 'name' => $this->customer->get_first_name() . ' ' . $this->customer->get_last_name()];
            $this->model_account_activity->add_activity('address_add', $activity_data);
        }
    }
    /**
     * Edit Address
     *
     * Trigger
     *
     * catalog/model/account/address/editAddress/after
     *
     * @param array<int, mixed> $args
     * @param mixed             $output
     *
     */
    public function edit_address(string &$route, array &$args, &$output): void
    {
        // Activity
        if ($this->config->get('config_customer_activity')) {
            $this->load->model('account/activity');
            $activity_data = ['customer_id' => $this->customer->get_id(), 'name' => $this->customer->get_first_name() . ' ' . $this->customer->get_last_name()];
            $this->model_account_activity->add_activity('address_edit', $activity_data);
        }
    }
    /**
     * Delete Address
     *
     * Trigger
     *
     * catalog/model/account/address/deleteAddress/after
     *
     * @param array<int, mixed> $args
     * @param mixed             $output
     *
     */
    public function delete_address(string &$route, array &$args, &$output): void
    {
        // Activity
        if ($this->config->get('config_customer_activity')) {
            $this->load->model('account/activity');
            $activity_data = ['customer_id' => $this->customer->get_id(), 'name' => $this->customer->get_first_name() . ' ' . $this->customer->get_last_name()];
            $this->model_account_activity->add_activity('address_delete', $activity_data);
        }
    }
    /**
     * Add Return
     *
     * Trigger
     *
     * catalog/model/account/returns/addReturn/after
     *
     * @param array<int, mixed> $args
     * @param mixed             $output
     *
     */
    public function add_return(string &$route, array &$args, &$output): void
    {
        // Activity
        if ($this->config->get('config_customer_activity') && $output) {
            $this->load->model('account/activity');
            if ($this->customer->is_logged()) {
                $activity_data = ['customer_id' => $this->customer->get_id(), 'name' => $this->customer->get_first_name() . ' ' . $this->customer->get_last_name(), 'return_id' => $output];
                $this->model_account_activity->add_activity('return_account', $activity_data);
            } else {
                $activity_data = ['name' => $args[0]['firstname'] . ' ' . $args[0]['lastname'], 'return_id' => $output];
                $this->model_account_activity->add_activity('return_guest', $activity_data);
            }
        }
    }
    /**
     * Add History
     *
     * Trigger
     *
     * catalog/model/checkout/order/addHistory/before
     *
     * @param array<int, mixed> $args
     *
     */
    public function add_history(string &$route, array &$args): void
    {
        // Customer
        if ($this->config->get('config_customer_activity')) {
            // If the last order status id returns 0, and the new order status is not, then we record it as new order
            $this->load->model('checkout/order');
            $order_info = $this->model_checkout_order->get_order($args[0]);
            // Activity
            if ($order_info && !$order_info['order_status_id'] && $args[1]) {
                $this->load->model('account/activity');
                if ($order_info['customer_id']) {
                    $activity_data = ['customer_id' => $order_info['customer_id'], 'name' => $order_info['firstname'] . ' ' . $order_info['lastname'], 'order_id' => $args[0]];
                    $this->model_account_activity->add_activity('order_account', $activity_data);
                } else {
                    $activity_data = ['name' => $order_info['firstname'] . ' ' . $order_info['lastname'], 'order_id' => $args[0]];
                    $this->model_account_activity->add_activity('order_guest', $activity_data);
                }
            }
        }
    }
}