<?php

declare (strict_types=1);
namespace Opencart\Admin\Controller\Marketing;

/**
 * Class Marketing
 *
 * @package Opencart\Admin\Controller\Marketing
 */
class Marketing extends \Opencart\System\Engine\Controller
{
    /**
     * Index
     */
    public function index(): void
    {
        $this->load->language('marketing/marketing');
        if (isset($this->request->get['filter_name'])) {
            $filter_name = $this->request->get['filter_name'];
        } else {
            $filter_name = '';
        }
        if (isset($this->request->get['filter_code'])) {
            $filter_code = $this->request->get['filter_code'];
        } else {
            $filter_code = '';
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
        $allowed = ['filter_name', 'filter_code', 'filter_date_from', 'filter_date_to', 'filter_date_to', 'page'];
        $url = '&' . http_build_query(array_intersect_key($this->request->get, array_flip($allowed)));
        $data['breadcrumbs'] = [];
        $data['breadcrumbs'][] = ['text' => $this->language->get('text_home'), 'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])];
        $data['breadcrumbs'][] = ['text' => $this->language->get('heading_title'), 'href' => $this->url->link('marketing/marketing', 'user_token=' . $this->session->data['user_token'] . $url)];
        $data['add'] = $this->url->link('marketing/marketing.form', 'user_token=' . $this->session->data['user_token'] . $url);
        $data['delete'] = $this->url->link('marketing/marketing.delete', 'user_token=' . $this->session->data['user_token']);
        $data['list'] = $this->get_list();
        $data['filter_name'] = $filter_name;
        $data['filter_code'] = $filter_code;
        $data['filter_date_from'] = $filter_date_from;
        $data['filter_date_to'] = $filter_date_to;
        $data['user_token'] = $this->session->data['user_token'];
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->set_output($this->load->view('marketing/marketing', $data));
    }
    /**
     * List
     */
    public function list(): void
    {
        $this->load->language('marketing/marketing');
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
        if (isset($this->request->get['filter_code'])) {
            $filter_code = $this->request->get['filter_code'];
        } else {
            $filter_code = '';
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
        if (isset($this->request->get['page'])) {
            $page = (int) $this->request->get['page'];
        } else {
            $page = 1;
        }
        $allowed = ['filter_name', 'filter_code', 'filter_date_from', 'filter_date_to', 'filter_date_to', 'page'];
        $url = '&' . http_build_query(array_intersect_key($this->request->get, array_flip($allowed)));
        $data['action'] = $this->url->link('marketing/marketing.list', 'user_token=' . $this->session->data['user_token'] . $url);
        $data['marketings'] = [];
        // Marketing
        $filter_data = ['filter_name' => $filter_name, 'filter_code' => $filter_code, 'filter_date_from' => $filter_date_from, 'filter_date_to' => $filter_date_to, 'start' => ($page - 1) * $this->config->get('config_pagination_admin'), 'limit' => $this->config->get('config_pagination_admin')];
        $this->load->model('marketing/marketing');
        $results = $this->model_marketing_marketing->get_marketings($filter_data);
        foreach ($results as $result) {
            $data['marketings'][] = ['edit' => $this->url->link('marketing/marketing.form', 'user_token=' . $this->session->data['user_token'] . '&marketing_id=' . $result['marketing_id'] . $url)] + $result;
        }
        $allowed = ['filter_name', 'filter_code', 'filter_date_from', 'filter_date_to', 'filter_date_to'];
        $marketing_total = $this->model_marketing_marketing->get_total_marketings($filter_data);
        // Pagination
        $data['total'] = $marketing_total;
        $data['page'] = $page;
        $data['limit'] = $this->config->get('config_pagination_admin');
        $data['pagination'] = $this->url->link('marketing/marketing.history', 'user_token=' . $this->session->data['user_token'] . '&page={page}');
        $data['results'] = sprintf($this->language->get('text_pagination'), $marketing_total ? ($page - 1) * $this->config->get('config_pagination_admin') + 1 : 0, ($page - 1) * $this->config->get('config_pagination_admin') > $marketing_total - $this->config->get('config_pagination_admin') ? $marketing_total : ($page - 1) * $this->config->get('config_pagination_admin') + $this->config->get('config_pagination_admin'), $marketing_total, ceil($marketing_total / $this->config->get('config_pagination_admin')));
        return $this->load->view('marketing/marketing_list', $data);
    }
    /**
     * Form
     */
    public function form(): void
    {
        $this->load->language('marketing/marketing');
        $this->document->set_title($this->language->get('heading_title'));
        $data['text_form'] = !isset($this->request->get['marketing_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
        $allowed = ['filter_name', 'filter_code', 'filter_date_from', 'filter_date_to', 'filter_date_to', 'page'];
        $url = '&' . http_build_query(array_intersect_key($this->request->get, array_flip($allowed)));
        $data['breadcrumbs'] = [];
        $data['breadcrumbs'][] = ['text' => $this->language->get('text_home'), 'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])];
        $data['breadcrumbs'][] = ['text' => $this->language->get('heading_title'), 'href' => $this->url->link('marketing/marketing', 'user_token=' . $this->session->data['user_token'] . $url)];
        $data['save'] = $this->url->link('marketing/marketing.save', 'user_token=' . $this->session->data['user_token']);
        $data['back'] = $this->url->link('marketing/marketing', 'user_token=' . $this->session->data['user_token'] . $url);
        if (isset($this->request->get['marketing_id'])) {
            $this->load->model('marketing/marketing');
            $marketing_info = $this->model_marketing_marketing->get_marketing($this->request->get['marketing_id']);
        }
        if (!empty($marketing_info)) {
            $data['marketing_id'] = $marketing_info['marketing_id'];
        } else {
            $data['marketing_id'] = 0;
        }
        $data['store'] = HTTP_CATALOG;
        if (!empty($marketing_info)) {
            $data['name'] = $marketing_info['name'];
        } else {
            $data['name'] = '';
        }
        if (!empty($marketing_info)) {
            $data['description'] = $marketing_info['description'];
        } else {
            $data['description'] = '';
        }
        if (!empty($marketing_info)) {
            $data['code'] = $marketing_info['code'];
        } else {
            $data['code'] = uniqid();
        }
        $data['report'] = $this->get_report();
        $data['user_token'] = $this->session->data['user_token'];
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->set_output($this->load->view('marketing/marketing_form', $data));
    }
    /**
     * Save
     */
    public function save(): void
    {
        $this->load->language('marketing/marketing');
        $json = [];
        if (!$this->user->has_permission('modify', 'marketing/marketing')) {
            $json['error']['warning'] = $this->language->get('error_permission');
        }
        $required = ['marketing_id' => 0, 'name' => '', 'description' => '', 'code' => ''];
        $post_info = $this->request->post + $required;
        if (!oc_validate_length($post_info['name'], 1, 32)) {
            $json['error']['name'] = $this->language->get('error_name');
        }
        if (!$post_info['code']) {
            $json['error']['code'] = $this->language->get('error_code');
        }
        // Marketing
        $this->load->model('marketing/marketing');
        $marketing_info = $this->model_marketing_marketing->get_marketing_by_code($post_info['code']);
        if ($marketing_info && (!$post_info['marketing_id'] || $post_info['marketing_id'] != $marketing_info['marketing_id'])) {
            $json['error']['code'] = $this->language->get('error_exists');
        }
        if (!$json) {
            if (!$post_info['marketing_id']) {
                $json['marketing_id'] = $this->model_marketing_marketing->add_marketing($post_info);
            } else {
                $this->model_marketing_marketing->edit_marketing($post_info['marketing_id'], $post_info);
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
        $this->load->language('marketing/marketing');
        $json = [];
        if (isset($this->request->post['selected'])) {
            $selected = (array) $this->request->post['selected'];
        } else {
            $selected = [];
        }
        if (!$this->user->has_permission('modify', 'marketing/marketing')) {
            $json['error'] = $this->language->get('error_permission');
        }
        if (!$json) {
            $this->load->model('marketing/marketing');
            foreach ($selected as $marketing_id) {
                $this->model_marketing_marketing->delete_marketing($marketing_id);
            }
            $json['success'] = $this->language->get('text_success');
        }
        $this->response->add_header('Content-Type: application/json');
        $this->response->set_output(json_encode($json));
    }
    /**
     * Report
     */
    public function report(): void
    {
        $this->load->language('marketing/marketing');
        $this->response->set_output($this->get_report());
    }
    /**
     * Get Report
     */
    public function get_report(): string
    {
        if (isset($this->request->get['marketing_id'])) {
            $marketing_id = (int) $this->request->get['marketing_id'];
        } else {
            $marketing_id = 0;
        }
        if (isset($this->request->get['page']) && $this->request->get['route'] == 'marketing/marketing.report') {
            $page = (int) $this->request->get['page'];
        } else {
            $page = 1;
        }
        $limit = 10;
        $data['reports'] = [];
        // Marketing
        $this->load->model('marketing/marketing');
        // Customer
        $this->load->model('customer/customer');
        // Setting
        $this->load->model('setting/store');
        $results = $this->model_marketing_marketing->get_reports($marketing_id, ($page - 1) * $limit, $limit);
        foreach ($results as $result) {
            $store_info = $this->model_setting_store->get_store($result['store_id']);
            if ($store_info) {
                $store = $store_info['name'];
            } elseif (!$result['store_id']) {
                $store = $this->config->get('config_name');
            } else {
                $store = '';
            }
            $data['reports'][] = ['ip' => $result['ip'], 'account' => $this->model_customer_customer->get_total_customers_by_ip($result['ip']), 'store' => $store, 'country' => $result['country'], 'date_added' => date($this->language->get('datetime_format'), strtotime($result['date_added'])), 'filter_ip' => $this->url->link('customer/customer', 'user_token=' . $this->session->data['user_token'] . '&filter_ip=' . $result['ip'])];
        }
        // Total Reports
        $report_total = $this->model_marketing_marketing->get_total_reports($marketing_id);
        // Pagination
        $data['total'] = $report_total;
        $data['page'] = $page;
        $data['limit'] = $this->config->get('config_pagination_admin');
        $data['pagination'] = $this->url->link('marketing/marketing.report', 'user_token=' . $this->session->data['user_token'] . '&marketing_id=' . $marketing_id . '&page={page}');
        $data['results'] = sprintf($this->language->get('text_pagination'), $report_total ? ($page - 1) * $limit + 1 : 0, ($page - 1) * $limit > $report_total - $limit ? $report_total : ($page - 1) * $limit + $limit, $report_total, ceil($report_total / $limit));
        return $this->load->view('marketing/marketing_report', $data);
    }
    /**
     * Autocomplete
     */
    public function autocomplete(): void
    {
        $json = [];
        if (isset($this->request->get['filter_name'])) {
            $filter_name = $this->request->get['filter_name'];
        } else {
            $filter_name = '';
        }
        if (isset($this->request->get['filter_code'])) {
            $filter_code = $this->request->get['filter_code'];
        } else {
            $filter_code = '';
        }
        // Marketing
        $filter_data = ['filter_name' => $filter_name, 'filter_code' => $filter_code, 'start' => 0, 'limit' => $this->config->get('config_autocomplete_limit')];
        $this->load->model('marketing/marketing');
        $results = $this->model_marketing_marketing->get_marketings($filter_data);
        foreach ($results as $result) {
            $json[] = ['marketing_id' => $result['marketing_id'], 'name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')), 'code' => $result['code']];
        }
        $sort_order = [];
        foreach ($json as $key => $value) {
            $sort_order[$key] = $value['name'];
        }
        array_multisort($sort_order, SORT_ASC, $json);
        $this->response->add_header('Content-Type: application/json');
        $this->response->set_output(json_encode($json));
    }
}