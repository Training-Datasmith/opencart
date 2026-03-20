<?php

declare (strict_types=1);
namespace Opencart\Catalog\Controller\Api;

/**
 * Class Subscription
 *
 * Subscription API
 *
 * @package Opencart\Catalog\Controller\Api
 */
class Subscription extends \Opencart\System\Engine\Controller
{
    /**
     * Index
     */
    public function index(): void
    {
        $this->load->language('api/subscription');
        if (isset($this->request->get['call'])) {
            $call = $this->request->get['call'];
        } else {
            $call = '';
        }
        // Allowed calls
        $output = match ($call) {
            'cart' => $this->get_cart(),
            'product_add' => $this->add_product(),
            'shipping_methods' => $this->get_shipping_methods(),
            'payment_methods' => $this->get_payment_methods(),
            'payment_methods' => $this->get_payment_methods(),
            'confirm' => $this->confirm(),
            'history_add' => $this->add_history(),
            default => ['error' => $this->language->get('error_call')],
        };
        $this->response->add_header('Content-Type: application/json');
        $this->response->set_output(json_encode($output));
    }
    /**
     * Set Customer
     *
     * @return array<string, mixed>
     */
    protected function set_customer(): array
    {
        $this->load->language('api/order');
        $output = [];
        // Customer
        if (isset($this->request->post['customer_id'])) {
            $customer_id = (int) $this->request->post['customer_id'];
        } else {
            $customer_id = 0;
        }
        $this->load->model('account/customer');
        $customer_info = $this->model_account_customer->get_customer($customer_id);
        if (!$customer_info) {
            $output['error'] = $this->language->get('error_customer');
        }
        if (!$output) {
            // Log the customer in
            $this->customer->login($customer_info['email'], '', true);
            $this->session->data['customer'] = $customer_info;
            $output['success'] = $this->language->get('text_success');
        }
        return $output;
    }
    /**
     * Set Payment Address
     *
     * @return array<string, mixed>
     */
    protected function set_payment_address(): array
    {
        $this->load->language('api/order');
        $output = [];
        if (isset($this->request->post['payment_address_id'])) {
            $address_id = (int) $this->request->post['payment_address_id'];
        } else {
            $address_id = 0;
        }
        // Payment Address
        $this->load->model('account/address');
        $address_info = $this->model_account_address->get_address($this->customer->get_id(), $address_id);
        if (!$address_info) {
            $output['error'] = $this->language->get('error_payment_address');
        }
        if (!$output) {
            $this->session->data['payment_address'] = $address_info;
            $output['success'] = $this->language->get('text_success');
        }
        return $output;
    }
    /**
     * Set Shipping Address
     *
     * @return array<string, mixed>
     */
    protected function set_shipping_address(): array
    {
        $this->load->language('api/order');
        $output = [];
        if (isset($this->request->post['shipping_address_id'])) {
            $address_id = (int) $this->request->post['shipping_address_id'];
        } else {
            $address_id = 0;
        }
        // Shipping Address
        $this->load->model('account/address');
        $address_info = $this->model_account_address->get_address($this->customer->get_id(), $address_id);
        if (!$address_info) {
            $output['error'] = $this->language->get('error_shipping_address');
        }
        if (!$output) {
            $this->session->data['shipping_address'] = $address_info;
            $output['success'] = $this->language->get('text_success');
        }
        return $output;
    }
    /**
     * Get Shipping Methods
     *
     * @return array<string, mixed>
     */
    protected function get_shipping_methods(): array
    {
        $this->set_customer();
        $output = $this->load->controller('api/cart');
        if (isset($output['error'])) {
            return $output;
        }
        $this->set_payment_address();
        $this->set_shipping_address();
        return $this->load->controller('api/shipping_method.getShippingMethods');
    }
    /**
     * Get Payment Methods
     *
     * @return array<string, mixed>
     */
    protected function get_payment_methods(): array
    {
        $this->set_customer();
        $output = $this->load->controller('api/cart');
        if (isset($output['error'])) {
            return $output;
        }
        $this->set_payment_address();
        $this->set_shipping_address();
        $this->load->controller('api/shipping_method');
        return $this->load->controller('api/payment_method.getPaymentMethods');
    }
    /**
     * Set Payment Method
     *
     * @return array<string, mixed>
     */
    protected function set_payment_method(): array
    {
        $this->set_customer();
        $output = $this->load->controller('api/cart');
        if (isset($output['error'])) {
            return $output;
        }
        $this->set_payment_address();
        $this->set_shipping_address();
        $this->load->controller('api/shipping_method');
        $output = $this->load->controller('api/payment_method');
        $output['products'] = $this->load->controller('api/cart.getProducts');
        $output['shipping_required'] = $this->cart->has_shipping();
        return $output;
    }
    /**
     * Get Cart
     *
     * @return array<string, mixed>
     */
    protected function get_cart(): array
    {
        $this->set_customer();
        // If any errors at the cart level, such as products don't exist, then we want to return the error
        $output = $this->load->controller('api/cart');
        $this->set_payment_address();
        $this->set_shipping_address();
        $this->load->controller('api/shipping_method');
        $this->load->controller('api/payment_method');
        $output['products'] = $this->load->controller('api/cart.getProducts');
        $output['shipping_required'] = $this->cart->has_shipping();
        return $output;
    }
    /**
     * Add Product
     *
     * @return array<string, mixed>
     */
    protected function add_product(): array
    {
        $this->set_customer();
        $output = $this->load->controller('api/cart');
        if (isset($output['error'])) {
            return $output;
        }
        $this->set_payment_address();
        $this->set_shipping_address();
        $output = $this->load->controller('api/cart.addProduct');
        $output['products'] = $this->load->controller('api/cart.getProducts');
        $output['shipping_required'] = $this->cart->has_shipping();
        return $output;
    }
    /**
     * Confirm
     *
     * @return array<string, mixed>
     */
    protected function confirm(): array
    {
        $this->set_customer();
        $output = $this->load->controller('api/cart');
        if (isset($output['error'])) {
            return $output;
        }
        $output = [];
        // Add keys for missing post vars
        $required = ['subscription_id' => 0, 'subscription_plan_id' => 0];
        $post_info = $this->request->post + $required;
        $this->set_payment_address();
        $this->set_shipping_address();
        $this->load->controller('api/shipping_method');
        $this->load->controller('api/payment_method');
        $this->load->language('sale/subscription');
        // 1. Validate customer data exists
        if (!isset($this->session->data['customer'])) {
            $output['error']['customer'] = $this->language->get('error_customer');
        }
        // Subscription Plan
        $this->load->model('catalog/subscription_plan');
        $subscription_plan_info = $this->model_catalog_subscription_plan->get_subscription_plan($post_info['subscription_plan_id']);
        if (!$subscription_plan_info) {
            $output['error']['subscription_plan'] = $this->language->get('error_subscription_plan');
        }
        // 2. Validate cart has products.
        if (!$this->cart->has_products()) {
            $output['error']['product'] = $this->language->get('error_product');
        }
        // 3. Validate cart has products and has stock
        if (!$this->cart->has_stock() && !$this->config->get('config_stock_checkout') || !$this->cart->has_minimum()) {
            $output['error']['product'] = $this->language->get('error_stock');
        }
        // 4. Validate payment address if required
        if ($this->config->get('config_checkout_payment_address') && !isset($this->session->data['payment_address'])) {
            $output['error']['payment_address'] = $this->language->get('error_payment_address');
        }
        // 5. Validate shipping address and method, if required
        if ($this->cart->has_shipping()) {
            // Shipping Address
            if (!isset($this->session->data['shipping_address'])) {
                $output['error']['shipping_address'] = $this->language->get('error_shipping_address');
            }
            // Validate shipping method
            if (!isset($this->session->data['shipping_method'])) {
                $output['error']['shipping_method'] = $this->language->get('error_shipping_method');
            }
        } else {
            unset($this->session->data['shipping_address']);
            unset($this->session->data['shipping_method']);
        }
        // 6. Validate payment method
        if (!isset($this->session->data['payment_method'])) {
            $output['error']['payment_method'] = $this->language->get('error_payment_method');
        }
        if (!$output) {
            // Subscription
            $subscription_product_data = [];
            $products = $this->cart->get_subscriptions();
            foreach ($products as $product) {
                $subscription_product_data[] = ['order_product_id' => 0, 'order_id' => 0, 'trial_price' => $product['subscription']['trial_price'], 'trial_tax' => $this->tax->get_tax($product['subscription']['trial_price'], $product['tax_class_id']), 'price' => $product['subscription']['price'], 'tax' => $this->tax->get_tax($product['subscription']['price'] * $product['quantity'], $product['tax_class_id'])] + $product + $product['subscription'];
            }
            $subscription_data = $subscription_plan_info + ['subscription_product' => $subscription_product_data, 'trial_price' => array_sum(array_column($subscription_product_data, 'trial_price')), 'trial_tax' => array_sum(array_column($subscription_product_data, 'trial_tax')), 'price' => array_sum(array_column($subscription_product_data, 'price')), 'tax' => array_sum(array_column($subscription_product_data, 'tax')), 'store_id' => $this->config->get('config_store_id'), 'language' => $this->config->get('config_language'), 'currency' => $this->session->data['currency']];
            $this->load->model('checkout/subscription');
            if (!$post_info['subscription_plan_id']) {
                $output['subscription_id'] = $this->model_checkout_subscription->add_subscription($post_info + $subscription_data);
            } else {
                $this->model_checkout_subscription->edit_subscription((int) $post_info['subscription_id'], $post_info + $subscription_data);
            }
            $output['success'] = $this->language->get('text_success');
        }
        $output['products'] = $this->load->controller('api/cart.getProducts');
        $output['shipping_required'] = $this->cart->has_shipping();
        return $output;
    }
    /**
     * Add History
     *
     * @return array<string, mixed>
     */
    protected function add_history(): array
    {
        $this->load->language('api/subscription');
        $output = [];
        // Add keys for missing post vars
        $required = ['subscription_id' => 0, 'subscription_status_id' => 0, 'comment' => '', 'notify' => 0];
        $post_info = $this->request->post + $required;
        // Subscription
        $this->load->model('checkout/subscription');
        $subscription_info = $this->model_checkout_subscription->get_subscription((int) $post_info['subscription_id']);
        if (!$subscription_info) {
            $output['error'] = $this->language->get('error_subscription');
        }
        if (!$output) {
            $this->model_checkout_order->add_history((int) $post_info['subscription_id'], (int) $post_info['subscription_status_id'], (string) $post_info['comment'], (bool) $post_info['notify']);
            $output['success'] = $this->language->get('text_success');
        }
        return $output;
    }
}