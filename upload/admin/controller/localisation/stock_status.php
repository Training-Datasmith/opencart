<?php

declare (strict_types=1);
namespace Opencart\Admin\Controller\Localisation;

/**
 * Class Stock Status
 *
 * @package Opencart\Admin\Controller\Localisation
 */
class Stock_Status extends \Opencart\System\Engine\Controller
{
    /**
     * Index
     */
    public function index(): void
    {
        $this->load->language('localisation/stock_status');
        $this->document->set_title($this->language->get('heading_title'));
        $allowed = ['sort', 'order', 'page'];
        $url = '&' . http_build_query(array_intersect_key($this->request->get, array_flip($allowed)));
        $data['breadcrumbs'] = [];
        $data['breadcrumbs'][] = ['text' => $this->language->get('text_home'), 'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])];
        $data['breadcrumbs'][] = ['text' => $this->language->get('heading_title'), 'href' => $this->url->link('localisation/stock_status', 'user_token=' . $this->session->data['user_token'] . $url)];
        $data['add'] = $this->url->link('localisation/stock_status.form', 'user_token=' . $this->session->data['user_token'] . $url);
        $data['delete'] = $this->url->link('localisation/stock_status.delete', 'user_token=' . $this->session->data['user_token']);
        $data['list'] = $this->get_list();
        $data['user_token'] = $this->session->data['user_token'];
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->set_output($this->load->view('localisation/stock_status', $data));
    }
    /**
     * List
     */
    public function list(): void
    {
        $this->load->language('localisation/stock_status');
        $this->response->set_output($this->get_list());
    }
    /**
     * Get List
     */
    public function get_list(): string
    {
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
        $allowed = ['sort', 'order', 'page'];
        $url = '&' . http_build_query(array_intersect_key($this->request->get, array_flip($allowed)));
        $data['action'] = $this->url->link('localisation/stock_status.list', 'user_token=' . $this->session->data['user_token'] . $url);
        // Stock Statuses
        $data['stock_statuses'] = [];
        $filter_data = ['sort' => $sort, 'order' => $order, 'start' => ($page - 1) * $this->config->get('config_pagination_admin'), 'limit' => $this->config->get('config_pagination_admin')];
        $this->load->model('localisation/stock_status');
        $results = $this->model_localisation_stock_status->get_stock_statuses($filter_data);
        foreach ($results as $result) {
            $data['stock_statuses'][] = ['edit' => $this->url->link('localisation/stock_status.form', 'user_token=' . $this->session->data['user_token'] . '&stock_status_id=' . $result['stock_status_id'] . $url)] + $result;
        }
        // Total Stock Statuses
        $stock_status_total = $this->model_localisation_stock_status->get_total_stock_statuses();
        // Pagination
        $data['total'] = $stock_status_total;
        $data['page'] = $page;
        $data['limit'] = $this->config->get('config_pagination_admin');
        $data['pagination'] = $this->url->link('localisation/stock_status.list', 'user_token=' . $this->session->data['user_token'] . '&page={page}');
        $data['results'] = sprintf($this->language->get('text_pagination'), $stock_status_total ? ($page - 1) * $this->config->get('config_pagination_admin') + 1 : 0, ($page - 1) * $this->config->get('config_pagination_admin') > $stock_status_total - $this->config->get('config_pagination_admin') ? $stock_status_total : ($page - 1) * $this->config->get('config_pagination_admin') + $this->config->get('config_pagination_admin'), $stock_status_total, ceil($stock_status_total / $this->config->get('config_pagination_admin')));
        $data['sort'] = $sort;
        $data['order'] = $order;
        return $this->load->view('localisation/stock_status_list', $data);
    }
    /**
     * Form
     */
    public function form(): void
    {
        $this->load->language('localisation/stock_status');
        $this->document->set_title($this->language->get('heading_title'));
        $data['text_form'] = !isset($this->request->get['stock_status_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
        $url = '';
        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }
        $data['breadcrumbs'] = [];
        $data['breadcrumbs'][] = ['text' => $this->language->get('text_home'), 'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])];
        $data['breadcrumbs'][] = ['text' => $this->language->get('heading_title'), 'href' => $this->url->link('localisation/stock_status', 'user_token=' . $this->session->data['user_token'] . $url)];
        $data['save'] = $this->url->link('localisation/stock_status.save', 'user_token=' . $this->session->data['user_token']);
        $data['back'] = $this->url->link('localisation/stock_status', 'user_token=' . $this->session->data['user_token'] . $url);
        if (isset($this->request->get['stock_status_id'])) {
            $data['stock_status_id'] = (int) $this->request->get['stock_status_id'];
        } else {
            $data['stock_status_id'] = 0;
        }
        // Languages
        $this->load->model('localisation/language');
        $data['languages'] = $this->model_localisation_language->get_languages();
        if (isset($this->request->get['stock_status_id'])) {
            $this->load->model('localisation/stock_status');
            $data['stock_status'] = $this->model_localisation_stock_status->get_descriptions((int) $this->request->get['stock_status_id']);
        } else {
            $data['stock_status'] = [];
        }
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->set_output($this->load->view('localisation/stock_status_form', $data));
    }
    /**
     * Save
     */
    public function save(): void
    {
        $this->load->language('localisation/stock_status');
        $json = [];
        if (!$this->user->has_permission('modify', 'localisation/stock_status')) {
            $json['error']['warning'] = $this->language->get('error_permission');
        }
        $required = ['stock_status_id' => 0, 'stock_status' => []];
        $post_info = $this->request->post + $required;
        foreach ($post_info['stock_status'] as $language_id => $value) {
            if (!oc_validate_length($value['name'], 3, 32)) {
                $json['error']['name_' . $language_id] = $this->language->get('error_name');
            }
        }
        if (!$json) {
            // Stock Status
            $this->load->model('localisation/stock_status');
            if (!$post_info['stock_status_id']) {
                $json['stock_status_id'] = $this->model_localisation_stock_status->add_stock_status($post_info);
            } else {
                $this->model_localisation_stock_status->edit_stock_status($post_info['stock_status_id'], $post_info);
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
        $this->load->language('localisation/stock_status');
        $json = [];
        if (isset($this->request->post['selected'])) {
            $selected = (array) $this->request->post['selected'];
        } else {
            $selected = [];
        }
        if (!$this->user->has_permission('modify', 'localisation/stock_status')) {
            $json['error'] = $this->language->get('error_permission');
        }
        // Product
        $this->load->model('catalog/product');
        foreach ($selected as $stock_status_id) {
            // Total Products
            $product_total = $this->model_catalog_product->get_total_products_by_stock_status_id($stock_status_id);
            if ($product_total) {
                $json['error'] = sprintf($this->language->get('error_product'), $product_total);
            }
        }
        if (!$json) {
            // Stock Status
            $this->load->model('localisation/stock_status');
            foreach ($selected as $stock_status_id) {
                $this->model_localisation_stock_status->delete_stock_status($stock_status_id);
            }
            $json['success'] = $this->language->get('text_success');
        }
        $this->response->add_header('Content-Type: application/json');
        $this->response->set_output(json_encode($json));
    }
}