<?php

declare (strict_types=1);
namespace Opencart\Admin\Controller\Customer;

/**
 * Class Customer
 *
 * Can be loaded using $this->load->controller('customer/customer');
 *
 * @package Opencart\Admin\Controller\Customer
 */
class Customer extends \Opencart\System\Engine\Controller
{
    /**
     * Index
     */
    public function index(): void
    {
        $this->load->language('customer/customer');
        $this->document->set_title($this->language->get('heading_title'));
        $this->document->add_script('view/javascript/customer.js');
        if (isset($this->request->get['filter_name'])) {
            $filter_name = (string) $this->request->get['filter_name'];
        } else {
            $filter_name = '';
        }
        if (isset($this->request->get['filter_email'])) {
            $filter_email = (string) $this->request->get['filter_email'];
        } else {
            $filter_email = '';
        }
        if (isset($this->request->get['filter_customer_group_id'])) {
            $filter_customer_group_id = (int) $this->request->get['filter_customer_group_id'];
        } else {
            $filter_customer_group_id = '';
        }
        if (isset($this->request->get['filter_status'])) {
            $filter_status = (bool) $this->request->get['filter_status'];
        } else {
            $filter_status = '';
        }
        if (isset($this->request->get['filter_ip'])) {
            $filter_ip = (string) $this->request->get['filter_ip'];
        } else {
            $filter_ip = '';
        }
        if (isset($this->request->get['filter_date_from'])) {
            $filter_date_from = (string) $this->request->get['filter_date_from'];
        } else {
            $filter_date_from = '';
        }
        if (isset($this->request->get['filter_date_to'])) {
            $filter_date_to = (string) $this->request->get['filter_date_to'];
        } else {
            $filter_date_to = '';
        }
        $allowed = ['filter_name', 'filter_email', 'filter_customer_group_id', 'filter_status', 'filter_ip', 'filter_date_from', 'filter_date_to', 'sort', 'order', 'page'];
        $url = '&' . http_build_query(array_intersect_key($this->request->get, array_flip($allowed)));
        $data['breadcrumbs'] = [];
        $data['breadcrumbs'][] = ['text' => $this->language->get('text_home'), 'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])];
        $data['breadcrumbs'][] = ['text' => $this->language->get('heading_title'), 'href' => $this->url->link('customer/customer', 'user_token=' . $this->session->data['user_token'] . $url)];
        $data['add'] = $this->url->link('customer/customer.form', 'user_token=' . $this->session->data['user_token'] . $url);
        $data['delete'] = $this->url->link('customer/customer.delete', 'user_token=' . $this->session->data['user_token']);
        $data['enable'] = $this->url->link('customer/customer.enable', 'user_token=' . $this->session->data['user_token']);
        $data['disable'] = $this->url->link('customer/customer.disable', 'user_token=' . $this->session->data['user_token']);
        $data['list'] = $this->get_list();
        // Customer Groups
        $this->load->model('customer/customer_group');
        $data['customer_groups'] = $this->model_customer_customer_group->get_customer_groups();
        $data['filter_name'] = $filter_name;
        $data['filter_email'] = $filter_email;
        $data['filter_customer_group_id'] = $filter_customer_group_id;
        $data['filter_status'] = $filter_status;
        $data['filter_ip'] = $filter_ip;
        $data['filter_date_from'] = $filter_date_from;
        $data['filter_date_to'] = $filter_date_to;
        $data['user_token'] = $this->session->data['user_token'];
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->set_output($this->load->view('customer/customer', $data));
    }
    /**
     * List
     */
    public function list(): void
    {
        $this->load->language('customer/customer');
        $this->response->set_output($this->get_list());
    }
    /**
     * Get List
     */
    public function get_list(): string
    {
        if (isset($this->request->get['filter_name'])) {
            $filter_name = $this->request->get['filter_name'];
        } else {
            $filter_name = '';
        }
        if (isset($this->request->get['filter_email'])) {
            $filter_email = $this->request->get['filter_email'];
        } else {
            $filter_email = '';
        }
        if (isset($this->request->get['filter_customer_group_id'])) {
            $filter_customer_group_id = (int) $this->request->get['filter_customer_group_id'];
        } else {
            $filter_customer_group_id = '';
        }
        if (isset($this->request->get['filter_status'])) {
            $filter_status = (bool) $this->request->get['filter_status'];
        } else {
            $filter_status = '';
        }
        if (isset($this->request->get['filter_ip'])) {
            $filter_ip = (string) $this->request->get['filter_ip'];
        } else {
            $filter_ip = '';
        }
        if (isset($this->request->get['filter_date_from'])) {
            $filter_date_from = (string) $this->request->get['filter_date_from'];
        } else {
            $filter_date_from = '';
        }
        if (isset($this->request->get['filter_date_to'])) {
            $filter_date_to = (string) $this->request->get['filter_date_to'];
        } else {
            $filter_date_to = '';
        }
        if (isset($this->request->get['sort'])) {
            $sort = (string) $this->request->get['sort'];
        } else {
            $sort = 'name';
        }
        if (isset($this->request->get['order'])) {
            $order = (string) $this->request->get['order'];
        } else {
            $order = 'ASC';
        }
        if (isset($this->request->get['page'])) {
            $page = (int) $this->request->get['page'];
        } else {
            $page = 1;
        }
        $allowed = ['filter_name', 'filter_email', 'filter_customer_group_id', 'filter_status', 'filter_ip', 'filter_date_from', 'filter_date_to', 'sort', 'order', 'page'];
        $url = '&' . http_build_query(array_intersect_key($this->request->get, array_flip($allowed)));
        $data['action'] = $this->url->link('customer/customer.list', 'user_token=' . $this->session->data['user_token'] . $url);
        // Setting
        $data['stores'] = [];
        $data['stores'][] = ['store_id' => 0, 'name' => $this->config->get('config_name')];
        $this->load->model('setting/store');
        $data['stores'] = array_merge($data['stores'], $this->model_setting_store->get_stores());
        // Customers
        $data['customers'] = [];
        $filter_data = ['filter_name' => $filter_name, 'filter_email' => $filter_email, 'filter_customer_group_id' => $filter_customer_group_id, 'filter_status' => $filter_status, 'filter_ip' => $filter_ip, 'filter_date_from' => $filter_date_from, 'filter_date_to' => $filter_date_to, 'sort' => $sort, 'order' => $order, 'start' => ($page - 1) * $this->config->get('config_pagination_admin'), 'limit' => $this->config->get('config_pagination_admin')];
        $this->load->model('customer/customer');
        $results = $this->model_customer_customer->get_customers($filter_data);
        foreach ($results as $result) {
            $login_info = $this->model_customer_customer->get_total_login_attempts($result['email']);
            if ($login_info && $login_info['total'] >= $this->config->get('config_login_attempts')) {
                $unlock = $this->url->link('customer/customer.unlock', 'user_token=' . $this->session->data['user_token'] . '&email=' . $result['email'] . $url);
            } else {
                $unlock = '';
            }
            $store_data = [];
            foreach ($data['stores'] as $store) {
                $store_data[] = ['href' => $this->url->link('customer/customer.login', 'user_token=' . $this->session->data['user_token'] . '&customer_id=' . $result['customer_id'] . '&store_id=' . $store['store_id'])] + $store;
            }
            $data['customers'][] = ['date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])), 'unlock' => $unlock, 'store' => $store_data, 'edit' => $this->url->link('customer/customer.form', 'user_token=' . $this->session->data['user_token'] . '&customer_id=' . $result['customer_id'] . $url)] + $result;
        }
        $allowed = ['filter_name', 'filter_email', 'filter_customer_group_id', 'filter_status', 'filter_ip', 'filter_date_from', 'filter_date_to'];
        $url = '&' . http_build_query(array_intersect_key($this->request->get, array_flip($allowed)));
        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }
        // Sorts
        $data['sort_name'] = $this->url->link('customer/customer.list', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url);
        $data['sort_email'] = $this->url->link('customer/customer.list', 'user_token=' . $this->session->data['user_token'] . '&sort=email' . $url);
        $data['sort_customer_group'] = $this->url->link('customer/customer.list', 'user_token=' . $this->session->data['user_token'] . '&sort=customer_group' . $url);
        $data['sort_status'] = $this->url->link('customer/customer.list', 'user_token=' . $this->session->data['user_token'] . '&sort=status' . $url);
        $data['sort_date_added'] = $this->url->link('customer/customer.list', 'user_token=' . $this->session->data['user_token'] . '&sort=date_added' . $url);
        $allowed = ['filter_name', 'filter_email', 'filter_customer_group_id', 'filter_status', 'filter_ip', 'filter_date_from', 'filter_date_to', 'sort', 'order'];
        $url = '&' . http_build_query(array_intersect_key($this->request->get, array_flip($allowed)));
        // Total Customers
        $customer_total = $this->model_customer_customer->get_total_customers($filter_data);
        // Pagination
        $data['total'] = $customer_total;
        $data['page'] = $page;
        $data['limit'] = $this->config->get('config_pagination_admin');
        $data['pagination'] = $this->url->link('customer/customer.list', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}');
        $data['results'] = sprintf($this->language->get('text_pagination'), $customer_total ? ($page - 1) * $this->config->get('config_pagination_admin') + 1 : 0, ($page - 1) * $this->config->get('config_pagination_admin') > $customer_total - $this->config->get('config_pagination_admin') ? $customer_total : ($page - 1) * $this->config->get('config_pagination_admin') + $this->config->get('config_pagination_admin'), $customer_total, ceil($customer_total / $this->config->get('config_pagination_admin')));
        $data['sort'] = $sort;
        $data['order'] = $order;
        return $this->load->view('customer/customer_list', $data);
    }
    /**
     * Form
     */
    public function form(): void
    {
        $this->load->language('customer/customer');
        $this->document->set_title($this->language->get('heading_title'));
        $this->document->add_script('view/javascript/customer.js');
        $data['text_form'] = !isset($this->request->get['customer_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
        $data['error_upload_size'] = sprintf($this->language->get('error_upload_size'), $this->config->get('config_file_max_size'));
        $data['config_file_max_size'] = (int) $this->config->get('config_file_max_size') * 1024 * 1024;
        $data['config_telephone_required'] = $this->config->get('config_telephone_required');
        $allowed = ['filter_name', 'filter_email', 'filter_customer_group_id', 'filter_status', 'filter_ip', 'filter_date_from', 'filter_date_to', 'sort', 'order', 'page'];
        $url = '&' . http_build_query(array_intersect_key($this->request->get, array_flip($allowed)));
        $data['breadcrumbs'] = [];
        $data['breadcrumbs'][] = ['text' => $this->language->get('text_home'), 'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])];
        $data['breadcrumbs'][] = ['text' => $this->language->get('heading_title'), 'href' => $this->url->link('customer/customer', 'user_token=' . $this->session->data['user_token'] . $url)];
        $data['save'] = $this->url->link('customer/customer.save', 'user_token=' . $this->session->data['user_token']);
        $data['back'] = $this->url->link('customer/customer', 'user_token=' . $this->session->data['user_token'] . $url);
        $data['upload'] = $this->url->link('tool/upload.upload', 'user_token=' . $this->session->data['user_token']);
        if (isset($this->request->get['customer_id'])) {
            $data['order'] = $this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'] . '&filter_customer_id=' . $this->request->get['customer_id']);
            $data['return'] = $this->url->link('sale/return', 'user_token=' . $this->session->data['user_token'] . '&filter_customer_id=' . $this->request->get['customer_id']);
            $data['subscription'] = $this->url->link('sale/subscription', 'user_token=' . $this->session->data['user_token'] . '&filter_customer_id=' . $this->request->get['customer_id']);
        } else {
            $data['order'] = '';
            $data['return'] = '';
            $data['subscription'] = '';
        }
        // Customer
        if (isset($this->request->get['customer_id'])) {
            $this->load->model('customer/customer');
            $customer_info = $this->model_customer_customer->get_customer((int) $this->request->get['customer_id']);
        }
        if (!empty($customer_info)) {
            $data['customer_id'] = $customer_info['customer_id'];
        } else {
            $data['customer_id'] = 0;
        }
        // Stores
        $data['stores'] = [];
        $data['stores'][] = ['store_id' => 0, 'name' => $this->config->get('config_name')];
        $this->load->model('setting/store');
        $data['stores'] = array_merge($data['stores'], $this->model_setting_store->get_stores());
        if (!empty($customer_info)) {
            $data['store_id'] = $customer_info['store_id'];
        } else {
            $data['store_id'] = 0;
        }
        // Languages
        $this->load->model('localisation/language');
        $data['languages'] = $this->model_localisation_language->get_languages();
        if (!empty($customer_info)) {
            $data['language_id'] = $customer_info['language_id'];
        } else {
            $data['language_id'] = 0;
        }
        // Customer Groups
        $this->load->model('customer/customer_group');
        $data['customer_groups'] = $this->model_customer_customer_group->get_customer_groups();
        if (!empty($customer_info)) {
            $data['customer_group_id'] = $customer_info['customer_group_id'];
        } else {
            $data['customer_group_id'] = (int) $this->config->get('config_customer_group_id');
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
            $data['email'] = $customer_info['email'];
        } else {
            $data['email'] = '';
        }
        if (!empty($customer_info)) {
            $data['telephone'] = $customer_info['telephone'];
        } else {
            $data['telephone'] = '';
        }
        // Custom Fields
        $data['custom_fields'] = [];
        $filter_data = ['filter_location' => 'account', 'sort' => 'cf.sort_order', 'order' => 'ASC'];
        $this->load->model('customer/custom_field');
        $custom_fields = $this->model_customer_custom_field->get_custom_fields($filter_data);
        foreach ($custom_fields as $custom_field) {
            if ($custom_field['status']) {
                $data['custom_fields'][] = ['custom_field_value' => $this->model_customer_custom_field->get_values($custom_field['custom_field_id']), 'value' => $custom_field['value']] + $custom_field;
            }
        }
        if (!empty($customer_info)) {
            $data['account_custom_field'] = $customer_info['custom_field'];
        } else {
            $data['account_custom_field'] = [];
        }
        $data['password'] = '';
        $data['confirm'] = '';
        if (!empty($customer_info)) {
            $data['newsletter'] = $customer_info['newsletter'];
        } else {
            $data['newsletter'] = 0;
        }
        if (!empty($customer_info)) {
            $data['status'] = $customer_info['status'];
        } else {
            $data['status'] = 1;
        }
        if (!empty($customer_info)) {
            $data['safe'] = $customer_info['safe'];
        } else {
            $data['safe'] = 0;
        }
        if (!empty($customer_info)) {
            $data['commenter'] = $customer_info['commenter'];
        } else {
            $data['commenter'] = 0;
        }
        // Countries
        $data['address'] = $this->load->controller('customer/address.getAddress');
        $data['history'] = $this->get_history();
        $data['transaction'] = $this->get_transaction();
        $data['reward'] = $this->get_reward();
        $data['ip'] = $this->get_ip();
        $data['authorize'] = $this->get_authorize();
        $data['user_token'] = $this->session->data['user_token'];
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->set_output($this->load->view('customer/customer_form', $data));
    }
    /**
     * Save
     */
    public function save(): void
    {
        $this->load->language('customer/customer');
        $json = [];
        if (!$this->user->has_permission('modify', 'customer/customer')) {
            $json['error']['warning'] = $this->language->get('error_permission');
        }
        $required = ['customer_id' => 0, 'store_id' => 0, 'language_id' => 0, 'customer_group_id' => 0, 'firstname' => '', 'lastname' => '', 'email' => '', 'telephone' => '', 'custom_field' => [], 'newsletter' => 0, 'password' => '', 'status' => 0, 'safe' => 0, 'commenter' => 0];
        $post_info = $this->request->post + $required;
        if (!oc_validate_length($post_info['firstname'], 1, 32)) {
            $json['error']['firstname'] = $this->language->get('error_firstname');
        }
        if (!oc_validate_length($post_info['lastname'], 1, 32)) {
            $json['error']['lastname'] = $this->language->get('error_lastname');
        }
        if (!oc_validate_email($post_info['email'])) {
            $json['error']['email'] = $this->language->get('error_email');
        }
        // Customer
        $this->load->model('customer/customer');
        $customer_info = $this->model_customer_customer->get_customer_by_email($post_info['email']);
        if ($customer_info && (!$post_info['customer_id'] && $post_info['customer_id'] != $customer_info['customer_id'])) {
            $json['error']['warning'] = $this->language->get('error_exists');
        }
        if ($this->config->get('config_telephone_required') && !oc_validate_length($post_info['telephone'], 3, 32)) {
            $json['error']['telephone'] = $this->language->get('error_telephone');
        }
        // Custom fields validation
        $filter_data = ['filter_location' => 'account', 'filter_customer_group_id' => $post_info['customer_group_id'], 'filter_status' => 1];
        $this->load->model('customer/custom_field');
        $custom_fields = $this->model_customer_custom_field->get_custom_fields($filter_data);
        foreach ($custom_fields as $custom_field) {
            if ($custom_field['required'] && empty($post_info['custom_field'][$custom_field['custom_field_id']])) {
                $json['error']['custom_field_' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
            } elseif ($custom_field['type'] == 'text' && !empty($custom_field['validation']) && !oc_validate_regex($post_info['custom_field'][$custom_field['custom_field_id']], $custom_field['validation'])) {
                $json['error']['custom_field_' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_regex'), $custom_field['name']);
            }
        }
        if ($post_info['password'] || !isset($post_info['customer_id'])) {
            $password = html_entity_decode($post_info['password'], ENT_QUOTES, 'UTF-8');
            if (!oc_validate_length($password, $this->config->get('config_password_length'), 40)) {
                $json['error']['password'] = sprintf($this->language->get('error_password_length'), $this->config->get('config_password_length'));
            }
            $required = [];
            if ($this->config->get('config_password_uppercase') && !preg_match('/[A-Z]/', $password)) {
                $required[] = $this->language->get('error_password_uppercase');
            }
            if ($this->config->get('config_password_lowercase') && !preg_match('/[a-z]/', $password)) {
                $required[] = $this->language->get('error_password_lowercase');
            }
            if ($this->config->get('config_password_number') && !preg_match('/[0-9]/', $password)) {
                $required[] = $this->language->get('error_password_number');
            }
            if ($this->config->get('config_password_symbol') && !preg_match('/[^a-zA-Z0-9]/', $password)) {
                $required[] = $this->language->get('error_password_symbol');
            }
            if ($required) {
                $json['error']['password'] = sprintf($this->language->get('error_password'), implode(', ', $required), $this->config->get('config_password_length'));
            }
            if ($post_info['password'] != $post_info['confirm']) {
                $json['error']['confirm'] = $this->language->get('error_confirm');
            }
        }
        if (isset($json['error']) && !isset($json['error']['warning'])) {
            $json['error']['warning'] = $this->language->get('error_warning');
        }
        if (!$json) {
            if (!$post_info['customer_id']) {
                $json['customer_id'] = $this->model_customer_customer->add_customer($post_info);
            } else {
                $this->model_customer_customer->edit_customer($post_info['customer_id'], $post_info);
            }
            $json['success'] = $this->language->get('text_success');
        }
        $this->response->add_header('Content-Type: application/json');
        $this->response->set_output(json_encode($json));
    }
    /**
     * Unlock
     */
    public function unlock(): void
    {
        $this->load->language('customer/customer');
        $json = [];
        if (!$this->user->has_permission('modify', 'customer/customer')) {
            $json['error'] = $this->language->get('error_permission');
        }
        if (empty($this->request->get['email'])) {
            $json['error'] = $this->language->get('error_email');
        }
        if (!$json) {
            // Customer
            $this->load->model('customer/customer');
            $this->model_customer_customer->delete_login_attempts($this->request->get['email']);
            $json['success'] = $this->language->get('text_success');
        }
        $this->response->add_header('Content-Type: application/json');
        $this->response->set_output(json_encode($json));
    }
    /**
     * Enable
     */
    public function enable(): void
    {
        $this->load->language('customer/customer');
        $json = [];
        if (isset($this->request->post['selected'])) {
            $selected = (array) $this->request->post['selected'];
        } else {
            $selected = [];
        }
        if (!$this->user->has_permission('modify', 'customer/customer')) {
            $json['error'] = $this->language->get('error_permission');
        }
        if (!$json) {
            $this->load->model('customer/customer');
            foreach ($selected as $customer_id) {
                $this->model_customer_customer->edit_status((int) $customer_id, true);
            }
            $json['success'] = $this->language->get('text_success');
        }
        $this->response->add_header('Content-Type: application/json');
        $this->response->set_output(json_encode($json));
    }
    /**
     * Disable
     */
    public function disable(): void
    {
        $this->load->language('customer/customer');
        $json = [];
        if (isset($this->request->post['selected'])) {
            $selected = (array) $this->request->post['selected'];
        } else {
            $selected = [];
        }
        if (!$this->user->has_permission('modify', 'customer/customer')) {
            $json['error'] = $this->language->get('error_permission');
        }
        if (!$json) {
            $this->load->model('customer/customer');
            foreach ($selected as $customer_id) {
                $this->model_customer_customer->edit_status((int) $customer_id, false);
            }
            $json['success'] = $this->language->get('text_success');
        }
        $this->response->add_header('Content-Type: application/json');
        $this->response->set_output(json_encode($json));
    }
    /**
     * Delete
     */
    public function delete(): void
    {
        $this->load->language('customer/customer');
        $json = [];
        if (isset($this->request->post['selected'])) {
            $selected = (array) $this->request->post['selected'];
        } else {
            $selected = [];
        }
        if (!$this->user->has_permission('modify', 'customer/customer')) {
            $json['error'] = $this->language->get('error_permission');
        }
        if (!$json) {
            // Customer
            $this->load->model('customer/customer');
            foreach ($selected as $customer_id) {
                $this->model_customer_customer->delete_customer($customer_id);
            }
            $json['success'] = $this->language->get('text_success');
        }
        $this->response->add_header('Content-Type: application/json');
        $this->response->set_output(json_encode($json));
    }
    /**
     * Login
     */
    public function login(): ?\Opencart\System\Engine\Action
    {
        if (isset($this->request->get['customer_id'])) {
            $customer_id = (int) $this->request->get['customer_id'];
        } else {
            $customer_id = 0;
        }
        if (isset($this->request->get['store_id'])) {
            $store_id = (int) $this->request->get['store_id'];
        } else {
            $store_id = 0;
        }
        if (!$this->user->has_permission('modify', 'customer/customer')) {
            return new \Opencart\System\Engine\Action('error/permission');
        }
        // Store
        if ($store_id) {
            $this->load->model('setting/store');
            $store_info = $this->model_setting_store->get_store($store_id);
            if (!$store_info) {
                return new \Opencart\System\Engine\Action('error/not_found');
            }
        }
        // Customer
        $this->load->model('customer/customer');
        $customer_info = $this->model_customer_customer->get_customer($customer_id);
        if (!$customer_info) {
            return new \Opencart\System\Engine\Action('error/not_found');
        }
        // Create login token
        $token = oc_token(32);
        $this->model_customer_customer->add_token($customer_id, 'login', $token);
        if ($store_id) {
            $this->response->redirect($store_info['url'] . 'index.php?route=account/login.token&email=' . urlencode($customer_info['email']) . '&code=' . $token);
        } else {
            $this->response->redirect(HTTP_CATALOG . 'index.php?route=account/login.token&email=' . urlencode($customer_info['email']) . '&code=' . $token);
        }
        return null;
    }
    /**
     * Payment
     */
    public function payment(): void
    {
        $this->load->language('customer/customer');
        $this->response->set_output($this->get_payment());
    }
    /**
     * Get Payment
     */
    private function get_payment(): string
    {
        if (isset($this->request->get['customer_id'])) {
            $customer_id = (int) $this->request->get['customer_id'];
        } else {
            $customer_id = 0;
        }
        if (isset($this->request->get['page']) && $this->request->get['route'] == 'customer/customer.payment') {
            $page = (int) $this->request->get['page'];
        } else {
            $page = 1;
        }
        $limit = 10;
        // Payment Methods
        $data['payment_methods'] = [];
        // Subscriptions
        $this->load->model('sale/subscription');
        $results = $this->model_sale_subscription->get_subscriptions(['filter_customer_id' => $customer_id]);
        foreach ($results as $result) {
            if (isset($result['image'])) {
                $image = DIR_IMAGE . 'payment/' . $result['image'];
            } else {
                $image = '';
            }
            $data['payment_methods'][] = ['image' => $image, 'date_expire' => date($this->language->get('date_format_short'), strtotime($result['date_expire'])), 'delete' => $this->url->link('customer/customer.deletePayment', 'user_token=' . $this->session->data['user_token'] . '&customer_payment_id=' . $result['customer_payment_id'])] + $result;
        }
        // Total Subscriptions
        $payment_total = $this->model_sale_subscription->get_total_subscriptions(['filter_customer_id' => $customer_id]);
        // Pagination
        $data['total'] = $payment_total;
        $data['page'] = $page;
        $data['limit'] = $this->config->get('config_pagination_admin');
        $data['pagination'] = $this->url->link('customer/customer.payment', 'user_token=' . $this->session->data['user_token'] . '&customer_id=' . $customer_id . '&page={page}');
        $data['results'] = sprintf($this->language->get('text_pagination'), $payment_total ? ($page - 1) * $limit + 1 : 0, ($page - 1) * $limit > $payment_total - $limit ? $payment_total : ($page - 1) * $limit + $limit, $payment_total, ceil($payment_total / $limit));
        return $this->load->view('customer/customer_payment', $data);
    }
    /**
     * Delete Payment
     */
    public function delete_payment(): void
    {
        $this->load->language('customer/customer');
        $json = [];
        if (isset($this->request->get['customer_payment_id'])) {
            $customer_payment_id = (int) $this->request->get['customer_payment_id'];
        } else {
            $customer_payment_id = 0;
        }
        if (!$this->user->has_permission('modify', 'customer/customer')) {
            $json['error'] = $this->language->get('error_permission');
        }
        if (!$json) {
            // Subscription
            $this->load->model('sale/subscription');
            $this->model_sale_subscription->delete_subscription_by_customer_payment_id($customer_payment_id);
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
        $this->load->language('customer/customer');
        $this->response->set_output($this->get_history());
    }
    /**
     * Get History
     */
    public function get_history(): string
    {
        if (isset($this->request->get['customer_id'])) {
            $customer_id = (int) $this->request->get['customer_id'];
        } else {
            $customer_id = 0;
        }
        if (isset($this->request->get['page']) && $this->request->get['route'] == 'customer/customer.history') {
            $page = (int) $this->request->get['page'];
        } else {
            $page = 1;
        }
        $limit = 10;
        // Histories
        $data['histories'] = [];
        $this->load->model('customer/customer');
        $results = $this->model_customer_customer->get_histories($customer_id, ($page - 1) * $limit, $limit);
        foreach ($results as $result) {
            $data['histories'][] = ['comment' => nl2br($result['comment']), 'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))] + $result;
        }
        // Total Histories
        $history_total = $this->model_customer_customer->get_total_histories($customer_id);
        // Pagination
        $data['total'] = $history_total;
        $data['page'] = $page;
        $data['limit'] = $this->config->get('config_pagination_admin');
        $data['pagination'] = $this->url->link('customer/customer.history', 'user_token=' . $this->session->data['user_token'] . '&customer_id=' . $customer_id . '&page={page}');
        $data['results'] = sprintf($this->language->get('text_pagination'), $history_total ? ($page - 1) * $limit + 1 : 0, ($page - 1) * $limit > $history_total - $limit ? $history_total : ($page - 1) * $limit + $limit, $history_total, ceil($history_total / $limit));
        return $this->load->view('customer/customer_history', $data);
    }
    /**
     * Add History
     */
    public function add_history(): void
    {
        $this->load->language('customer/customer');
        $json = [];
        if (isset($this->request->get['customer_id'])) {
            $customer_id = (int) $this->request->get['customer_id'];
        } else {
            $customer_id = 0;
        }
        if (!$this->user->has_permission('modify', 'customer/customer')) {
            $json['error'] = $this->language->get('error_permission');
        }
        // Customer
        $this->load->model('customer/customer');
        $customer_info = $this->model_customer_customer->get_customer($customer_id);
        if (!$customer_info) {
            $json['error'] = $this->language->get('error_customer');
        }
        if (!$json) {
            $this->model_customer_customer->add_history($customer_id, isset($this->request->post['comment']) ? (string) $this->request->post['comment'] : '');
            $json['success'] = $this->language->get('text_success');
        }
        $this->response->add_header('Content-Type: application/json');
        $this->response->set_output(json_encode($json));
    }
    /**
     * Transaction
     */
    public function transaction(): void
    {
        $this->load->language('customer/customer');
        $this->response->set_output($this->get_transaction());
    }
    /**
     * Get Transaction
     */
    public function get_transaction(): string
    {
        if (isset($this->request->get['customer_id'])) {
            $customer_id = (int) $this->request->get['customer_id'];
        } else {
            $customer_id = 0;
        }
        if (isset($this->request->get['page']) && $this->request->get['route'] == 'customer/customer.transaction') {
            $page = (int) $this->request->get['page'];
        } else {
            $page = 1;
        }
        $limit = 10;
        // Transactions
        $data['transactions'] = [];
        $this->load->model('customer/customer');
        $results = $this->model_customer_customer->get_transactions($customer_id, ($page - 1) * $limit, $limit);
        foreach ($results as $result) {
            $data['transactions'][] = ['date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))] + $result;
        }
        $data['balance'] = $this->model_customer_customer->get_transaction_total($customer_id);
        // Total Transactions
        $transaction_total = $this->model_customer_customer->get_total_transactions($customer_id);
        // Pagination
        $data['total'] = $transaction_total;
        $data['page'] = $page;
        $data['limit'] = $this->config->get('config_pagination_admin');
        $data['pagination'] = $this->url->link('customer/customer.transaction', 'user_token=' . $this->session->data['user_token'] . '&customer_id=' . $customer_id . '&page={page}');
        $data['results'] = sprintf($this->language->get('text_pagination'), $transaction_total ? ($page - 1) * $limit + 1 : 0, ($page - 1) * $limit > $transaction_total - $limit ? $transaction_total : ($page - 1) * $limit + $limit, $transaction_total, ceil($transaction_total / $limit));
        $data['currency'] = $this->config->get('config_currency');
        return $this->load->view('customer/customer_transaction', $data);
    }
    /**
     * Add Transaction
     */
    public function add_transaction(): void
    {
        $this->load->language('customer/customer');
        $json = [];
        if (isset($this->request->get['customer_id'])) {
            $customer_id = (int) $this->request->get['customer_id'];
        } else {
            $customer_id = 0;
        }
        if (!$this->user->has_permission('modify', 'customer/customer')) {
            $json['error'] = $this->language->get('error_permission');
        }
        $required = ['description' => '', 'amount' => 0.0];
        $post_info = $this->request->post + $required;
        // Customer
        $this->load->model('customer/customer');
        $customer_info = $this->model_customer_customer->get_customer($customer_id);
        if (!$customer_info) {
            $json['error'] = $this->language->get('error_customer');
        }
        if (!$json) {
            // Customer
            $this->load->model('customer/customer');
            $this->model_customer_customer->add_transaction($customer_id, (string) $post_info['description'], (float) $post_info['amount']);
            $json['success'] = $this->language->get('text_success');
        }
        $this->response->add_header('Content-Type: application/json');
        $this->response->set_output(json_encode($json));
    }
    /**
     * Reward
     */
    public function reward(): void
    {
        $this->load->language('customer/customer');
        $this->response->set_output($this->get_reward());
    }
    /**
     * Get Reward
     */
    public function get_reward(): string
    {
        if (isset($this->request->get['customer_id'])) {
            $customer_id = (int) $this->request->get['customer_id'];
        } else {
            $customer_id = 0;
        }
        if (isset($this->request->get['page']) && $this->request->get['route'] == 'customer/customer.reward') {
            $page = (int) $this->request->get['page'];
        } else {
            $page = 1;
        }
        $limit = 10;
        // Rewards
        $data['rewards'] = [];
        $this->load->model('customer/customer');
        $results = $this->model_customer_customer->get_rewards($customer_id, ($page - 1) * $limit, $limit);
        foreach ($results as $result) {
            $data['rewards'][] = ['date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))] + $result;
        }
        $data['balance'] = $this->model_customer_customer->get_reward_total($customer_id);
        // Total Rewards
        $reward_total = $this->model_customer_customer->get_total_rewards($customer_id);
        // Pagination
        $data['total'] = $reward_total;
        $data['page'] = $page;
        $data['limit'] = $this->config->get('config_pagination_admin');
        $data['pagination'] = $this->url->link('customer/customer.reward', 'user_token=' . $this->session->data['user_token'] . '&customer_id=' . $customer_id . '&page={page}');
        $data['results'] = sprintf($this->language->get('text_pagination'), $reward_total ? ($page - 1) * $limit + 1 : 0, ($page - 1) * $limit > $reward_total - $limit ? $reward_total : ($page - 1) * $limit + $limit, $reward_total, ceil($reward_total / $limit));
        return $this->load->view('customer/customer_reward', $data);
    }
    /**
     * Add Reward
     */
    public function add_reward(): void
    {
        $this->load->language('customer/customer');
        $json = [];
        if (isset($this->request->get['customer_id'])) {
            $customer_id = (int) $this->request->get['customer_id'];
        } else {
            $customer_id = 0;
        }
        if (!$this->user->has_permission('modify', 'customer/customer')) {
            $json['error'] = $this->language->get('error_permission');
        }
        $required = ['description' => '', 'points' => 0];
        $post_info = $this->request->post + $required;
        // Customer
        $this->load->model('customer/customer');
        $customer_info = $this->model_customer_customer->get_customer($customer_id);
        if (!$customer_info) {
            $json['error'] = $this->language->get('error_customer');
        }
        if (!$json) {
            // Customer
            $this->load->model('customer/customer');
            $this->model_customer_customer->add_reward($customer_id, (string) $post_info['description'], (int) $post_info['points']);
            $json['success'] = $this->language->get('text_success');
        }
        $this->response->add_header('Content-Type: application/json');
        $this->response->set_output(json_encode($json));
    }
    /**
     * Ip
     */
    public function ip(): void
    {
        $this->load->language('customer/customer');
        $this->response->set_output($this->get_ip());
    }
    /**
     * Get Ip
     */
    public function get_ip(): string
    {
        if (isset($this->request->get['customer_id'])) {
            $customer_id = (int) $this->request->get['customer_id'];
        } else {
            $customer_id = 0;
        }
        if (isset($this->request->get['page']) && $this->request->get['route'] == 'customer/customer.ip') {
            $page = (int) $this->request->get['page'];
        } else {
            $page = 1;
        }
        $limit = 10;
        $data['ips'] = [];
        // Customers
        $this->load->model('customer/customer');
        // Setting
        $this->load->model('setting/store');
        $results = $this->model_customer_customer->get_ips($customer_id, ($page - 1) * $limit, $limit);
        foreach ($results as $result) {
            $store_info = $this->model_setting_store->get_store($result['store_id']);
            if ($store_info) {
                $store = $store_info['name'];
            } elseif (!$result['store_id']) {
                $store = $this->config->get('config_name');
            } else {
                $store = '';
            }
            $data['ips'][] = ['account' => $this->model_customer_customer->get_total_customers_by_ip($result['ip']), 'store' => $store, 'date_added' => date($this->language->get('datetime_format'), strtotime($result['date_added'])), 'filter_ip' => $this->url->link('customer/customer', 'user_token=' . $this->session->data['user_token'] . '&filter_ip=' . $result['ip'])] + $result;
        }
        // Total Customers
        $ip_total = $this->model_customer_customer->get_total_ips($customer_id);
        // Pagination
        $data['total'] = $ip_total;
        $data['page'] = $page;
        $data['limit'] = $this->config->get('config_pagination_admin');
        $data['pagination'] = $this->url->link('customer/customer.ip', 'user_token=' . $this->session->data['user_token'] . '&customer_id=' . $customer_id . '&page={page}');
        $data['results'] = sprintf($this->language->get('text_pagination'), $ip_total ? ($page - 1) * $limit + 1 : 0, ($page - 1) * $limit > $ip_total - $limit ? $ip_total : ($page - 1) * $limit + $limit, $ip_total, ceil($ip_total / $limit));
        return $this->load->view('customer/customer_ip', $data);
    }
    /**
     * Authorize
     */
    public function authorize(): void
    {
        $this->load->language('customer/customer');
        $this->response->set_output($this->get_authorize());
    }
    /**
     * Get Authorize
     */
    public function get_authorize(): string
    {
        if (isset($this->request->get['customer_id'])) {
            $customer_id = (int) $this->request->get['customer_id'];
        } else {
            $customer_id = 0;
        }
        if (isset($this->request->get['page']) && $this->request->get['route'] == 'customer/customer.login') {
            $page = (int) $this->request->get['page'];
        } else {
            $page = 1;
        }
        $limit = 10;
        // Authorizes
        $data['authorizes'] = [];
        $this->load->model('customer/customer');
        $results = $this->model_customer_customer->get_authorizes($customer_id, ($page - 1) * $limit, $limit);
        foreach ($results as $result) {
            $data['authorizes'][] = ['date_added' => date($this->language->get('datetime_format'), strtotime($result['date_added'])), 'date_expire' => $result['date_expire'] ? date($this->language->get('date_format_short'), strtotime($result['date_expire'])) : '', 'delete' => $this->url->link('customer/customer.deleteAuthorize', 'user_token=' . $this->session->data['user_token'] . '&customer_authorize_id=' . $result['customer_authorize_id'])] + $result;
        }
        // Total Authorizes
        $authorize_total = $this->model_customer_customer->get_total_authorizes($customer_id);
        // Pagination
        $data['total'] = $authorize_total;
        $data['page'] = $page;
        $data['limit'] = $this->config->get('config_pagination_admin');
        $data['pagination'] = $this->url->link('customer/customer.authorize', 'user_token=' . $this->session->data['user_token'] . '&customer_id=' . $customer_id . '&page={page}');
        $data['results'] = sprintf($this->language->get('text_pagination'), $authorize_total ? ($page - 1) * $limit + 1 : 0, ($page - 1) * $limit > $authorize_total - $limit ? $authorize_total : ($page - 1) * $limit + $limit, $authorize_total, ceil($authorize_total / $limit));
        return $this->load->view('customer/customer_authorize', $data);
    }
    /**
     * Delete Authorize
     */
    public function delete_authorize(): void
    {
        $this->load->language('customer/customer');
        $json = [];
        if (isset($this->request->get['customer_authorize_id'])) {
            $customer_authorize_id = (int) $this->request->get['customer_authorize_id'];
        } else {
            $customer_authorize_id = 0;
        }
        if (isset($this->request->cookie['authorize'])) {
            $token = $this->request->cookie['authorize'];
        } else {
            $token = '';
        }
        if (!$this->user->has_permission('modify', 'customer/customer')) {
            $json['error'] = $this->language->get('error_permission');
        }
        // Authorize
        $this->load->model('customer/customer');
        $authorize_info = $this->model_customer_customer->get_authorize($customer_authorize_id);
        if (!$authorize_info) {
            $json['error'] = $this->language->get('error_authorize');
        }
        if (!$json) {
            $this->model_customer_customer->delete_authorizes($authorize_info['customer_id'], $customer_authorize_id);
            $json['success'] = $this->language->get('text_success');
        }
        $this->response->add_header('Content-Type: application/json');
        $this->response->set_output(json_encode($json));
    }
    /**
     * Autocomplete
     */
    public function autocomplete(): void
    {
        $json = [];
        if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_email'])) {
            if (isset($this->request->get['filter_name'])) {
                $filter_name = $this->request->get['filter_name'];
            } else {
                $filter_name = '';
            }
            if (isset($this->request->get['filter_email'])) {
                $filter_email = $this->request->get['filter_email'];
            } else {
                $filter_email = '';
            }
            // Customers
            $filter_data = ['filter_name' => $filter_name, 'filter_email' => $filter_email, 'start' => 0, 'limit' => $this->config->get('config_autocomplete_limit')];
            $this->load->model('customer/customer');
            $results = $this->model_customer_customer->get_customers($filter_data);
            foreach ($results as $result) {
                $json[] = ['name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')), 'address' => $this->model_customer_customer->get_addresses($result['customer_id'])] + $result;
            }
        }
        array_multisort(array_column($json, 'name'), SORT_ASC, $json);
        $this->response->add_header('Content-Type: application/json');
        $this->response->set_output(json_encode($json));
    }
    /**
     * Customfield
     */
    public function customfield(): void
    {
        $json = [];
        // Customer Group
        if (isset($this->request->get['customer_group_id'])) {
            $customer_group_id = (int) $this->request->get['customer_group_id'];
        } else {
            $customer_group_id = (int) $this->config->get('config_customer_group_id');
        }
        // Custom Fields
        $this->load->model('customer/custom_field');
        $custom_fields = $this->model_customer_custom_field->get_custom_fields(['filter_customer_group_id' => $customer_group_id]);
        foreach ($custom_fields as $custom_field) {
            $json[] = ['required' => empty($custom_field['required']) || $custom_field['required'] == 0 ? false : true] + $custom_field;
        }
        $this->response->add_header('Content-Type: application/json');
        $this->response->set_output(json_encode($json));
    }
}