<?php

declare (strict_types=1);
namespace Opencart\Admin\Controller\Sale;

/**
 * Class Subscription
 *
 * @package Opencart\Admin\Controller\Sale
 */
class Subscription extends \Opencart\System\Engine\Controller
{
    /**
     * Index
     */
    public function index(): void
    {
        $this->load->language('sale/subscription');
        if (isset($this->request->get['filter_subscription_id'])) {
            $filter_subscription_id = (int) $this->request->get['filter_subscription_id'];
        } else {
            $filter_subscription_id = '';
        }
        if (isset($this->request->get['filter_order_id'])) {
            $filter_order_id = $this->request->get['filter_order_id'];
        } else {
            $filter_order_id = '';
        }
        if (isset($this->request->get['filter_customer'])) {
            $filter_customer = $this->request->get['filter_customer'];
        } else {
            $filter_customer = '';
        }
        if (isset($this->request->get['filter_subscription_status_id'])) {
            $filter_subscription_status_id = (int) $this->request->get['filter_subscription_status_id'];
        } else {
            $filter_subscription_status_id = '';
        }
        if (isset($this->request->get['filter_date_from'])) {
            $filter_date_from = $this->request->get['filter_date_from'];
        } else {
            $filter_date_from = '';
        }
        if (isset($this->request->get['filter_date_to'])) {
            $filter_date_to = $this->request->get['filter_date_to'];
        } else {
            $filter_date_to = '';
        }
        $this->document->set_title($this->language->get('heading_title'));
        $allowed = ['filter_subscription_id', 'filter_order_id', 'filter_customer', 'filter_subscription_status_id', 'filter_date_from', 'filter_date_to', 'sort', 'order', 'page'];
        $url = '&' . http_build_query(array_intersect_key($this->request->get, array_flip($allowed)));
        $data['breadcrumbs'] = [];
        $data['breadcrumbs'][] = ['text' => $this->language->get('text_home'), 'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])];
        $data['breadcrumbs'][] = ['text' => $this->language->get('heading_title'), 'href' => $this->url->link('sale/subscription', 'user_token=' . $this->session->data['user_token'] . $url)];
        $data['add'] = $this->url->link('sale/subscription.info', 'user_token=' . $this->session->data['user_token'] . $url);
        $data['delete'] = $this->url->link('sale/subscription.delete', 'user_token=' . $this->session->data['user_token']);
        $data['list'] = $this->get_list();
        // Subscription Statuses
        $this->load->model('localisation/subscription_status');
        $data['subscription_statuses'] = $this->model_localisation_subscription_status->get_subscription_statuses();
        $data['filter_subscription_id'] = $filter_subscription_id;
        $data['filter_order_id'] = $filter_order_id;
        $data['filter_customer'] = $filter_customer;
        $data['filter_subscription_status_id'] = $filter_subscription_status_id;
        $data['filter_date_from'] = $filter_date_from;
        $data['filter_date_to'] = $filter_date_to;
        $data['user_token'] = $this->session->data['user_token'];
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->set_output($this->load->view('sale/subscription', $data));
    }
    /**
     * List
     */
    public function list(): void
    {
        $this->load->language('sale/subscription');
        $this->response->set_output($this->get_list());
    }
    /**
     * Get List
     */
    public function get_list(): string
    {
        if (isset($this->request->get['filter_subscription_id'])) {
            $filter_subscription_id = (int) $this->request->get['filter_subscription_id'];
        } else {
            $filter_subscription_id = '';
        }
        if (isset($this->request->get['filter_order_id'])) {
            $filter_order_id = $this->request->get['filter_order_id'];
        } else {
            $filter_order_id = '';
        }
        if (isset($this->request->get['filter_customer'])) {
            $filter_customer = $this->request->get['filter_customer'];
        } else {
            $filter_customer = '';
        }
        if (isset($this->request->get['filter_subscription_status_id'])) {
            $filter_subscription_status_id = (int) $this->request->get['filter_subscription_status_id'];
        } else {
            $filter_subscription_status_id = '';
        }
        if (isset($this->request->get['filter_date_from'])) {
            $filter_date_from = $this->request->get['filter_date_from'];
        } else {
            $filter_date_from = '';
        }
        if (isset($this->request->get['filter_date_to'])) {
            $filter_date_to = $this->request->get['filter_date_to'];
        } else {
            $filter_date_to = '';
        }
        if (isset($this->request->get['sort'])) {
            $sort = (string) $this->request->get['sort'];
        } else {
            $sort = 'subscription_id';
        }
        if (isset($this->request->get['order'])) {
            $order = (string) $this->request->get['order'];
        } else {
            $order = 'DESC';
        }
        if (isset($this->request->get['page'])) {
            $page = (int) $this->request->get['page'];
        } else {
            $page = 1;
        }
        $allowed = ['filter_subscription_id', 'filter_order_id', 'filter_customer', 'filter_subscription_status_id', 'filter_date_from', 'filter_date_to', 'sort', 'order', 'page'];
        $url = '&' . http_build_query(array_intersect_key($this->request->get, array_flip($allowed)));
        $data['action'] = $this->url->link('sale/subscription.list', 'user_token=' . $this->session->data['user_token'] . $url);
        // Subscriptions
        $data['subscriptions'] = [];
        $filter_data = ['filter_subscription_id' => $filter_subscription_id, 'filter_order_id' => $filter_order_id, 'filter_customer' => $filter_customer, 'filter_subscription_status_id' => $filter_subscription_status_id, 'filter_date_from' => $filter_date_from, 'filter_date_to' => $filter_date_to, 'order' => $order, 'sort' => $sort, 'start' => ($page - 1) * $this->config->get('config_pagination_admin'), 'limit' => $this->config->get('config_pagination_admin')];
        $this->load->model('sale/subscription');
        $results = $this->model_sale_subscription->get_subscriptions($filter_data);
        foreach ($results as $result) {
            $data['subscriptions'][] = ['date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])), 'view' => $this->url->link('sale/subscription.info', 'user_token=' . $this->session->data['user_token'] . '&subscription_id=' . $result['subscription_id'] . $url), 'order' => $this->url->link('sale/order.info', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $result['order_id'])] + $result;
        }
        $allowed = ['filter_subscription_id', 'filter_order_id', 'filter_customer', 'filter_subscription_status_id', 'filter_date_from', 'filter_date_to'];
        $url = '&' . http_build_query(array_intersect_key($this->request->get, array_flip($allowed)));
        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }
        // Sorts
        $data['sort_subscription'] = $this->url->link('sale/subscription.list', 'user_token=' . $this->session->data['user_token'] . '&sort=subscription_id' . $url);
        $data['sort_order'] = $this->url->link('sale/subscription.list', 'user_token=' . $this->session->data['user_token'] . '&sort=order_id' . $url);
        $data['sort_customer'] = $this->url->link('sale/subscription.list', 'user_token=' . $this->session->data['user_token'] . '&sort=customer' . $url);
        $data['sort_status'] = $this->url->link('sale/subscription.list', 'user_token=' . $this->session->data['user_token'] . '&sort=subscription_status' . $url);
        $data['sort_date_added'] = $this->url->link('sale/subscription.list', 'user_token=' . $this->session->data['user_token'] . '&sort=date_added' . $url);
        $allowed = ['filter_subscription_id', 'filter_order_id', 'filter_customer', 'filter_subscription_status_id', 'filter_date_from', 'filter_date_to', 'sort', 'order'];
        $url = '&' . http_build_query(array_intersect_key($this->request->get, array_flip($allowed)));
        // Total Subscriptions
        $subscription_total = $this->model_sale_subscription->get_total_subscriptions($filter_data);
        // Pagination
        $data['total'] = $subscription_total;
        $data['page'] = $page;
        $data['limit'] = $this->config->get('config_pagination_admin');
        $data['pagination'] = $this->url->link('sale/subscription.list', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}');
        $data['results'] = sprintf($this->language->get('text_pagination'), $subscription_total ? ($page - 1) * $this->config->get('config_pagination_admin') + 1 : 0, ($page - 1) * $this->config->get('config_pagination_admin') > $subscription_total - $this->config->get('config_pagination_admin') ? $subscription_total : ($page - 1) * $this->config->get('config_pagination_admin') + $this->config->get('config_pagination_admin'), $subscription_total, ceil($subscription_total / $this->config->get('config_pagination_admin')));
        $data['sort'] = $sort;
        $data['order'] = $order;
        return $this->load->view('sale/subscription_list', $data);
    }
    /**
     * Info
     */
    public function info(): void
    {
        $this->load->language('sale/subscription');
        if (isset($this->request->get['subscription_id'])) {
            $subscription_id = (int) $this->request->get['subscription_id'];
        } else {
            $subscription_id = 0;
        }
        $this->document->set_title($this->language->get('heading_title'));
        $data['text_form'] = !$subscription_id ? $this->language->get('text_add') : sprintf($this->language->get('text_edit'), $subscription_id);
        $allowed = ['filter_subscription_id', 'filter_order_id', 'filter_customer', 'filter_subscription_status_id', 'filter_date_from', 'filter_date_to', 'sort', 'order', 'page'];
        $url = '&' . http_build_query(array_intersect_key($this->request->get, array_flip($allowed)));
        $data['breadcrumbs'] = [];
        $data['breadcrumbs'][] = ['text' => $this->language->get('text_home'), 'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])];
        $data['breadcrumbs'][] = ['text' => $this->language->get('heading_title'), 'href' => $this->url->link('sale/subscription', 'user_token=' . $this->session->data['user_token'] . $url)];
        $data['back'] = $this->url->link('sale/subscription', 'user_token=' . $this->session->data['user_token'] . $url);
        // Subscription
        $this->load->model('sale/subscription');
        $subscription_info = $this->model_sale_subscription->get_subscription($subscription_id);
        if (!empty($subscription_info)) {
            $data['subscription_id'] = $subscription_info['subscription_id'];
        } else {
            $data['subscription_id'] = 0;
        }
        if (!empty($subscription_info)) {
            $data['date_added'] = date($this->language->get('date_format_short'), strtotime($subscription_info['date_added']));
        } else {
            $data['date_added'] = date($this->language->get('date_format_short'));
        }
        // Order
        $this->load->model('sale/order');
        if (!empty($subscription_info)) {
            $order_info = $this->model_sale_order->get_order($subscription_info['order_id']);
        }
        if (!empty($subscription_info)) {
            $data['order_id'] = $subscription_info['order_id'];
        } else {
            $data['order_id'] = 0;
        }
        if (!empty($order_info)) {
            $data['order_edit'] = $this->url->link('sale/order.info', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $order_info['order_id']);
        } else {
            $data['order_edit'] = '';
        }
        // Customer
        $this->load->model('customer/customer');
        if (!empty($subscription_info)) {
            $customer_info = $this->model_customer_customer->get_customer($subscription_info['customer_id']);
        }
        if (!empty($customer_info)) {
            $data['customer_id'] = $customer_info['customer_id'];
        } else {
            $data['customer_id'] = '';
        }
        if (!empty($customer_info)) {
            $data['firstname'] = $customer_info['firstname'];
        } else {
            $data['firstname'] = '';
        }
        if (!empty($customer_info)) {
            $data['lastname'] = $customer_info['lastname'];
        } else {
            $data['lastname'] = '';
        }
        if (!empty($customer_info)) {
            $data['customer_group_id'] = $customer_info['customer_group_id'];
        } else {
            $data['customer_group_id'] = (int) $this->config->get('config_customer_group_id');
        }
        if (!empty($customer_info)) {
            $data['addresses'] = $this->model_customer_customer->get_addresses($customer_info['customer_id']);
        } else {
            $data['addresses'] = [];
        }
        if (!empty($customer_info)) {
            $data['customer_edit'] = $this->url->link('customer/customer.form', 'user_token=' . $this->session->data['user_token'] . '&customer_id=' . $customer_info['customer_id']);
        } else {
            $data['customer_edit'] = '';
        }
        // Stores
        $data['stores'] = [];
        $data['stores'][] = ['store_id' => 0, 'name' => $this->config->get('config_name')];
        $this->load->model('setting/store');
        $data['stores'] = array_merge($data['stores'], $this->model_setting_store->get_stores());
        if (!empty($subscription_info)) {
            $data['store_id'] = $subscription_info['store_id'];
        } else {
            $data['store_id'] = (int) $this->config->get('config_store_id');
        }
        // Languages
        $this->load->model('localisation/language');
        $data['languages'] = $this->model_localisation_language->get_languages();
        if (!empty($subscription_info)) {
            $data['language_code'] = $subscription_info['language'];
        } else {
            $data['language_code'] = $this->config->get('config_language');
        }
        // Currencies
        $this->load->model('localisation/currency');
        $data['currencies'] = $this->model_localisation_currency->get_currencies();
        if (!empty($subscription_info)) {
            $data['currency_code'] = $subscription_info['currency'];
        } else {
            $data['currency_code'] = $this->config->get('config_currency');
        }
        // Subscription Plans
        $this->load->model('catalog/subscription_plan');
        $data['subscription_plans'] = $this->model_catalog_subscription_plan->get_subscription_plans();
        if (!empty($subscription_info)) {
            $data['subscription_plan_id'] = $subscription_info['subscription_plan_id'];
        } else {
            $data['subscription_plan_id'] = 0;
        }
        // Products
        $data['subscription_products'] = [];
        // Product
        $this->load->model('catalog/product');
        // Upload
        $this->load->model('tool/upload');
        $products = $this->model_sale_subscription->get_products($subscription_id);
        foreach ($products as $product) {
            $option_data = [];
            $options = $this->model_sale_subscription->get_options($product['subscription_id'], $product['subscription_product_id']);
            foreach ($options as $option) {
                if ($option['type'] != 'file') {
                    $option_data[] = $option;
                } else {
                    $upload_info = $this->model_tool_upload->get_upload_by_code($option['value']);
                    if ($upload_info) {
                        $option_data[] = ['filename' => $upload_info['mask'], 'href' => $this->url->link('tool/upload.download', 'user_token=' . $this->session->data['user_token'] . '&code=' . $upload_info['code'])] + $option;
                    }
                }
            }
            $data['subscription_products'][] = ['option' => $option_data, 'trial_price' => $product['trial_price'] * $product['quantity'], 'price' => $product['price'] * $product['quantity'], 'product_edit' => $this->url->link('catalog/product.form', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $product['product_id'])] + $product;
        }
        // Date next
        if (!empty($subscription_info)) {
            $data['date_next'] = date('Y-m-d', strtotime($subscription_info['date_next']));
        } else {
            $data['date_next'] = '';
        }
        if (!empty($subscription_info)) {
            $data['remaining'] = $subscription_info['remaining'];
        } else {
            $data['remaining'] = 0;
        }
        // Payment Address
        if (!empty($subscription_info)) {
            $address_info = $this->model_customer_customer->get_address($subscription_info['payment_address_id']);
        } else {
            $address_info = [];
        }
        if ($address_info) {
            $data['payment_address_id'] = $address_info['address_id'];
            $data['payment_firstname'] = $address_info['firstname'];
            $data['payment_lastname'] = $address_info['lastname'];
            $data['payment_company'] = $address_info['company'];
            $data['payment_address_1'] = $address_info['address_1'];
            $data['payment_address_2'] = $address_info['address_2'];
            $data['payment_city'] = $address_info['city'];
            $data['payment_postcode'] = $address_info['postcode'];
            $data['payment_country_id'] = $address_info['country_id'];
            $data['payment_country'] = $address_info['country'];
            $data['payment_zone_id'] = $address_info['zone_id'];
            $data['payment_zone'] = $address_info['zone'];
            $data['payment_custom_field'] = $address_info['custom_field'];
        } else {
            $data['payment_address_id'] = 0;
            $data['payment_firstname'] = '';
            $data['payment_lastname'] = '';
            $data['payment_company'] = '';
            $data['payment_address_1'] = '';
            $data['payment_address_2'] = '';
            $data['payment_city'] = '';
            $data['payment_postcode'] = '';
            $data['payment_country_id'] = 0;
            $data['payment_country'] = '';
            $data['payment_zone_id'] = 0;
            $data['payment_zone'] = '';
            $data['payment_custom_field'] = [];
        }
        // Payment Method
        if (!empty($subscription_info['payment_method'])) {
            $data['payment_method_name'] = $subscription_info['payment_method']['name'];
            $data['payment_method_code'] = $subscription_info['payment_method']['code'];
        } else {
            $data['payment_method_name'] = '';
            $data['payment_method_code'] = '';
        }
        // Shipping Address
        if (!empty($subscription_info)) {
            $address_info = $this->model_customer_customer->get_address($subscription_info['shipping_address_id']);
        } else {
            $address_info = [];
        }
        if ($address_info) {
            $data['shipping_address_id'] = $address_info['address_id'];
            $data['shipping_firstname'] = $address_info['firstname'];
            $data['shipping_lastname'] = $address_info['lastname'];
            $data['shipping_company'] = $address_info['company'];
            $data['shipping_address_1'] = $address_info['address_1'];
            $data['shipping_address_2'] = $address_info['address_2'];
            $data['shipping_city'] = $address_info['city'];
            $data['shipping_postcode'] = $address_info['postcode'];
            $data['shipping_country_id'] = $address_info['country_id'];
            $data['shipping_country'] = $address_info['country'];
            $data['shipping_zone_id'] = $address_info['zone_id'];
            $data['shipping_zone'] = $address_info['zone'];
            $data['shipping_custom_field'] = $address_info['custom_field'];
        } else {
            $data['shipping_address_id'] = 0;
            $data['shipping_firstname'] = '';
            $data['shipping_lastname'] = '';
            $data['shipping_company'] = '';
            $data['shipping_address_1'] = '';
            $data['shipping_address_2'] = '';
            $data['shipping_city'] = '';
            $data['shipping_postcode'] = '';
            $data['shipping_country_id'] = 0;
            $data['shipping_country'] = '';
            $data['shipping_zone_id'] = 0;
            $data['shipping_zone'] = '';
            $data['shipping_custom_field'] = [];
        }
        // Shipping Method
        if (!empty($subscription_info['shipping_method'])) {
            $data['shipping_method_name'] = $subscription_info['shipping_method']['name'];
            $data['shipping_method_code'] = $subscription_info['shipping_method']['code'];
            $data['shipping_method_cost'] = $subscription_info['shipping_method']['cost'];
            $data['shipping_method_tax_class_id'] = $subscription_info['shipping_method']['tax_class_id'];
        } else {
            $data['shipping_method_name'] = '';
            $data['shipping_method_code'] = '';
            $data['shipping_method_cost'] = 0.0;
            $data['shipping_method_tax_class_id'] = 0;
        }
        if (!empty($subscription_info)) {
            $data['comment'] = nl2br($subscription_info['comment']);
        } else {
            $data['comment'] = '';
        }
        // Subscription Statuses
        $this->load->model('localisation/subscription_status');
        $data['subscription_statuses'] = $this->model_localisation_subscription_status->get_subscription_statuses();
        if (!empty($subscription_info)) {
            $data['subscription_status_id'] = $subscription_info['subscription_status_id'];
        } else {
            $data['subscription_status_id'] = '';
        }
        // Histories
        $data['history'] = $this->get_history();
        // Orders
        $data['orders'] = $this->get_order();
        // Log
        $data['log'] = $this->get_log();
        // Additional tabs that are payment gateway specific
        $data['tabs'] = [];
        // Extension Order Tabs can be called here.
        $this->load->model('setting/extension');
        if (!empty($order_info)) {
            $extension_info = $this->model_setting_extension->get_extension_by_code('payment', $order_info['payment_method']['code']);
            if ($extension_info && $this->user->has_permission('access', 'extension/' . $extension_info['extension'] . '/payment/' . $extension_info['code'])) {
                $output = $this->load->controller('extension/payment/' . $order_info['payment_code'] . '.subscription');
                if (!$output instanceof \Exception) {
                    $this->load->language('extension/' . $extension_info['extension'] . '/payment/' . $extension_info['code'], 'extension');
                    $data['tabs'][] = ['code' => $extension_info['code'], 'title' => $this->language->get('extension_heading_title'), 'content' => $output];
                }
            }
        }
        $data['user_token'] = $this->session->data['user_token'];
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->set_output($this->load->view('sale/subscription_info', $data));
    }
    /**
     * Call
     *
     * Method to call the storefront API and return a response.
     *
     * @Example
     *
     * We create a hash from the data in a similar method to how amazon does things.
     *
     * $call     = 'order';
     * $username = 'API username';
     * $key      = 'API Key';
     * $domain   = 'www.yourdomain.com';
     * $path     = '/';
     * $store_id = 0;
     * $language = 'en-gb';
     * $time     = time();
     *
     * // Build hash string
     * $string  = $call . "\n";
     * $string .= $username . "\n";
     * $string .= $domain . "\n";
     * $string .= $path . "\n";
     * $string .= $store_id . "\n";
     * $string .= $language . "\n";
     * $string .= $currency . "\n";
     * $string .= json_encode($_POST) . "\n";
     * $string .= $time . "\n";
     *
     * $signature = base64_encode(hash_hmac('sha1', $string, $key, true));
     *
     * // Make remote call
     * $url  = '&call=' . $call;
     * $url  = '&username=' . urlencode($username);
     * $url .= '&store_id=' . $store_id;
     * $url .= '&language=' . $language;
     * $url .= '&currency=' . $currency;
     * $url .= '&time=' . $time;
     * $url .= '&signature=' . rawurlencode($signature);
     *
     * $curl = curl_init();
     *
     * curl_setopt($curl, CURLOPT_URL, 'https://' . $domain . $path . 'index.php?route=api/api' . $url);
     * curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
     * curl_setopt($curl, CURLOPT_HEADER, false);
     * curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
     * curl_setopt($curl, CURLOPT_TIMEOUT, 30);
     * curl_setopt($curl, CURLOPT_POST, 1);
     * curl_setopt($curl, CURLOPT_POSTFIELDS, $_POST);
     *
     * $response = curl_exec($curl);
     *
     * $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
     *
     * if ($status == 200) {
     *      $response_info = json_decode($response, true);
     * } else {
     *      $response_info = [];
     * }
     */
    public function call(): void
    {
        $this->load->language('sale/subscription');
        $json = [];
        if (isset($this->request->get['call'])) {
            $call = (string) $this->request->get['call'];
        } else {
            $call = '';
        }
        if (isset($this->request->get['store_id'])) {
            $store_id = (int) $this->request->get['store_id'];
        } else {
            $store_id = 0;
        }
        if (isset($this->request->get['language'])) {
            $language = (string) $this->request->get['language'];
        } else {
            $language = (string) $this->config->get('config_language');
        }
        if (isset($this->request->get['currency'])) {
            $currency = (string) $this->request->get['currency'];
        } else {
            $currency = (string) $this->config->get('config_currency');
        }
        if (!$this->user->has_permission('modify', 'sale/order')) {
            $json['error'] = $this->language->get('error_permission');
        }
        // Api
        $this->load->model('user/api');
        $api_info = $this->model_user_api->get_api((int) $this->config->get('config_api_id'));
        if (!$api_info) {
            $json['error'] = $this->language->get('error_api');
        }
        if (!$json) {
            // 1. Create a store instance using loader class to call controllers, models, views, libraries.
            $this->load->model('setting/store');
            $store = $this->model_setting_store->create_store_instance($store_id, $language, $currency);
            // Set the store ID.
            $store->config->set('config_store_id', $store_id);
            $store->session->data['currency'] = $currency;
            // 2. Remove the unneeded keys.
            $request_data = $this->request->get;
            unset($request_data['user_token']);
            // 3. Add the request GET vars.
            $store->request->get = $request_data;
            $store->request->get['route'] = 'api/subscription';
            // 4. Add the request POST var
            $store->request->post = $this->request->post;
            // 5. Call the required API controller.
            $store->load->controller($store->request->get['route']);
            // 6. Call the required API controller and get the output.
            $output = $store->response->get_output();
            // 7. Clean up data by clearing cart.
            $store->cart->clear();
            // 8. Deleting the current session, so we are not creating infinite sessions.
            $store->session->destroy();
        } else {
            $output = json_encode($json);
        }
        $this->response->add_header('Content-Type: application/json');
        $this->response->set_output($output);
    }
    /**
     * Delete
     */
    public function delete(): void
    {
        $this->load->language('sale/subscription');
        $json = [];
        if (isset($this->request->post['selected'])) {
            $selected = (array) $this->request->post['selected'];
        } else {
            $selected = [];
        }
        if (!$this->user->has_permission('modify', 'sale/subscription')) {
            $json['error'] = $this->language->get('error_permission');
        }
        if (!$json) {
            // Subscription
            $this->load->model('sale/subscription');
            foreach ($selected as $subscription_id) {
                $this->model_sale_subscription->delete_subscription($subscription_id);
            }
            $json['success'] = $this->language->get('text_success');
        }
        $this->response->add_header('Content-Type: application/json');
        $this->response->set_output(json_encode($json));
    }
    /**
     * History
     */
    public function history(): void
    {
        $this->load->language('sale/subscription');
        $this->response->set_output($this->get_history());
    }
    /**
     * Get History
     */
    public function get_history(): string
    {
        if (isset($this->request->get['subscription_id'])) {
            $subscription_id = (int) $this->request->get['subscription_id'];
        } else {
            $subscription_id = 0;
        }
        if (isset($this->request->get['page']) && $this->request->get['route'] == 'sale/subscription.history') {
            $page = (int) $this->request->get['page'];
        } else {
            $page = 1;
        }
        $limit = 10;
        $data['histories'] = [];
        // Histories
        $this->load->model('sale/subscription');
        $results = $this->model_sale_subscription->get_histories($subscription_id, ($page - 1) * $limit, $limit);
        foreach ($results as $result) {
            $data['histories'][] = ['comment' => nl2br($result['comment']), 'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))] + $result;
        }
        // Total Histories
        $subscription_total = $this->model_sale_subscription->get_total_histories($subscription_id);
        // Pagination
        $data['total'] = $subscription_total;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $data['pagination'] = $this->url->link('sale/subscription.history', 'user_token=' . $this->session->data['user_token'] . '&subscription_id=' . $subscription_id . '&page={page}');
        $data['results'] = sprintf($this->language->get('text_pagination'), $subscription_total ? ($page - 1) * $limit + 1 : 0, ($page - 1) * $limit > $subscription_total - $limit ? $subscription_total : ($page - 1) * $limit + $limit, $subscription_total, ceil($subscription_total / $limit));
        return $this->load->view('sale/subscription_history', $data);
    }
    /**
     * Add History
     */
    public function add_history(): void
    {
        $this->load->language('sale/subscription');
        $json = [];
        if (isset($this->request->get['subscription_id'])) {
            $subscription_id = (int) $this->request->get['subscription_id'];
        } else {
            $subscription_id = 0;
        }
        if (!$this->user->has_permission('modify', 'sale/subscription')) {
            $json['error'] = $this->language->get('error_permission');
        }
        $required = ['subscription_status_id' => 0, 'comment' => '', 'notify' => 0];
        $post_info = $this->request->post + $required;
        // Subscription
        $this->load->model('sale/subscription');
        $subscription_info = $this->model_sale_subscription->get_subscription($subscription_id);
        if (!$subscription_info) {
            $json['error'] = $this->language->get('error_subscription');
        }
        // Subscription Status
        $this->load->model('localisation/subscription_status');
        $subscription_status_info = $this->model_localisation_subscription_status->get_subscription_status($post_info['subscription_status_id']);
        if (!$subscription_status_info) {
            $json['error'] = $this->language->get('error_subscription_status');
        }
        if (!$json) {
            $this->model_sale_subscription->add_history($subscription_id, $post_info['subscription_status_id'], $post_info['comment'], $post_info['notify']);
            $json['success'] = $this->language->get('text_success');
        }
        $this->response->add_header('Content-Type: application/json');
        $this->response->set_output(json_encode($json));
    }
    /**
     * Order
     */
    public function order(): void
    {
        $this->load->language('sale/subscription');
        $this->response->set_output($this->get_order());
    }
    /**
     * Get Order
     */
    public function get_order(): string
    {
        if (isset($this->request->get['subscription_id'])) {
            $subscription_id = (int) $this->request->get['subscription_id'];
        } else {
            $subscription_id = 0;
        }
        if (isset($this->request->get['page']) && $this->request->get['route'] == 'sale/subscription.order') {
            $page = (int) $this->request->get['page'];
        } else {
            $page = 1;
        }
        $limit = 10;
        // Orders
        $data['orders'] = [];
        $this->load->model('sale/order');
        $results = $this->model_sale_order->get_orders_by_subscription_id($subscription_id, ($page - 1) * $limit, $limit);
        foreach ($results as $result) {
            $data['orders'][] = ['total' => $result['total'], 'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])), 'view' => $this->url->link('sale/order.info', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $result['order_id'])] + $result;
        }
        // Total Orders
        $order_total = $this->model_sale_order->get_total_orders_by_subscription_id($subscription_id);
        // Pagination
        $data['total'] = $order_total;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $data['pagination'] = $this->url->link('sale/subscription.order', 'user_token=' . $this->session->data['user_token'] . '&subscription_id=' . $subscription_id . '&page={page}');
        $data['results'] = sprintf($this->language->get('text_pagination'), $order_total ? ($page - 1) * $limit + 1 : 0, ($page - 1) * $limit > $order_total - $limit ? $order_total : ($page - 1) * $limit + $limit, $order_total, ceil($order_total / $limit));
        return $this->load->view('sale/subscription_order', $data);
    }
    /**
     * Logs
     */
    public function log(): void
    {
        $this->load->language('sale/subscription');
        $this->response->set_output($this->get_log());
    }
    /**
     * Get Logs
     */
    public function get_log(): string
    {
        if (isset($this->request->get['subscription_id'])) {
            $subscription_id = (int) $this->request->get['subscription_id'];
        } else {
            $subscription_id = 0;
        }
        if (isset($this->request->get['page']) && $this->request->get['route'] == 'sale/subscription.log') {
            $page = (int) $this->request->get['page'];
        } else {
            $page = 1;
        }
        $limit = 10;
        $data['logs'] = [];
        // Logs
        $this->load->model('sale/subscription');
        $results = $this->model_sale_subscription->get_logs($subscription_id, ($page - 1) * $limit, $limit);
        foreach ($results as $result) {
            $data['logs'][] = ['date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))] + $result;
        }
        // Total Subscriptions
        $subscription_total = $this->model_sale_subscription->get_total_logs($subscription_id);
        // Pagination
        $data['total'] = $subscription_total;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $data['pagination'] = $this->url->link('sale/subscription.log', 'user_token=' . $this->session->data['user_token'] . '&subscription_id=' . $subscription_id . '&page={page}');
        $data['results'] = sprintf($this->language->get('text_pagination'), $subscription_total ? ($page - 1) * $limit + 1 : 0, ($page - 1) * $limit > $subscription_total - $limit ? $subscription_total : ($page - 1) * $limit + $limit, $subscription_total, ceil($subscription_total / $limit));
        return $this->load->view('sale/subscription_log', $data);
    }
}