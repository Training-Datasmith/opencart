<?php

declare (strict_types=1);
namespace Opencart\Admin\Controller\Localisation;

/**
 * Class Return Reason
 *
 * @package Opencart\Admin\Controller\Localisation
 */
class Return_Reason extends \Opencart\System\Engine\Controller
{
    /**
     * Index
     */
    public function index(): void
    {
        $this->load->language('localisation/return_reason');
        $this->document->set_title($this->language->get('heading_title'));
        $url = '';
        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }
        $data['breadcrumbs'] = [];
        $data['breadcrumbs'][] = ['text' => $this->language->get('text_home'), 'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])];
        $data['breadcrumbs'][] = ['text' => $this->language->get('heading_title'), 'href' => $this->url->link('localisation/return_reason', 'user_token=' . $this->session->data['user_token'] . $url)];
        $data['add'] = $this->url->link('localisation/return_reason.form', 'user_token=' . $this->session->data['user_token'] . $url);
        $data['delete'] = $this->url->link('localisation/return_reason.delete', 'user_token=' . $this->session->data['user_token']);
        $data['list'] = $this->get_list();
        $data['user_token'] = $this->session->data['user_token'];
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->set_output($this->load->view('localisation/return_reason', $data));
    }
    /**
     * List
     */
    public function list(): void
    {
        $this->load->language('localisation/return_reason');
        $this->response->set_output($this->get_list());
    }
    /**
     * Get List
     */
    public function get_list(): string
    {
        if (isset($this->request->get['page'])) {
            $page = (int) $this->request->get['page'];
        } else {
            $page = 1;
        }
        $url = '';
        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }
        $data['action'] = $this->url->link('localisation/return_reason.list', 'user_token=' . $this->session->data['user_token'] . $url);
        // Return Reasons
        $data['return_reasons'] = [];
        $filter_data = ['start' => ($page - 1) * $this->config->get('config_pagination_admin'), 'limit' => $this->config->get('config_pagination_admin')];
        $this->load->model('localisation/return_reason');
        $results = $this->model_localisation_return_reason->get_return_reasons($filter_data);
        foreach ($results as $result) {
            $data['return_reasons'][] = ['edit' => $this->url->link('localisation/return_reason.form', 'user_token=' . $this->session->data['user_token'] . '&return_reason_id=' . $result['return_reason_id'] . $url)] + $result;
        }
        // Total Return Reasons
        $return_reason_total = $this->model_localisation_return_reason->get_total_return_reasons();
        // Pagination
        $data['total'] = $return_reason_total;
        $data['page'] = $page;
        $data['limit'] = $this->config->get('config_pagination_admin');
        $data['pagination'] = $this->url->link('localisation/return_reason.list', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}');
        $data['results'] = sprintf($this->language->get('text_pagination'), $return_reason_total ? ($page - 1) * $this->config->get('config_pagination_admin') + 1 : 0, ($page - 1) * $this->config->get('config_pagination_admin') > $return_reason_total - $this->config->get('config_pagination_admin') ? $return_reason_total : ($page - 1) * $this->config->get('config_pagination_admin') + $this->config->get('config_pagination_admin'), $return_reason_total, ceil($return_reason_total / $this->config->get('config_pagination_admin')));
        return $this->load->view('localisation/return_reason_list', $data);
    }
    /**
     * Form
     */
    public function form(): void
    {
        $this->load->language('localisation/return_reason');
        $this->document->set_title($this->language->get('heading_title'));
        $data['text_form'] = !isset($this->request->get['return_reason_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
        $url = '';
        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }
        $data['breadcrumbs'] = [];
        $data['breadcrumbs'][] = ['text' => $this->language->get('text_home'), 'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])];
        $data['breadcrumbs'][] = ['text' => $this->language->get('heading_title'), 'href' => $this->url->link('localisation/return_reason', 'user_token=' . $this->session->data['user_token'] . $url)];
        $data['save'] = $this->url->link('localisation/return_reason.save', 'user_token=' . $this->session->data['user_token']);
        $data['back'] = $this->url->link('localisation/return_reason', 'user_token=' . $this->session->data['user_token'] . $url);
        // Return Reason
        if (isset($this->request->get['return_reason_id'])) {
            $data['return_reason_id'] = (int) $this->request->get['return_reason_id'];
        } else {
            $data['return_reason_id'] = 0;
        }
        // Languages
        $this->load->model('localisation/language');
        $data['languages'] = $this->model_localisation_language->get_languages();
        if (isset($this->request->get['return_reason_id'])) {
            $this->load->model('localisation/return_reason');
            $data['return_reason'] = $this->model_localisation_return_reason->get_descriptions((int) $this->request->get['return_reason_id']);
        } else {
            $data['return_reason'] = [];
        }
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->set_output($this->load->view('localisation/return_reason_form', $data));
    }
    /**
     * Save
     */
    public function save(): void
    {
        $this->load->language('localisation/return_reason');
        $json = [];
        if (!$this->user->has_permission('modify', 'localisation/return_reason')) {
            $json['error']['warning'] = $this->language->get('error_permission');
        }
        $required = ['return_reason_id' => 0, 'return_reason' => []];
        $post_info = $this->request->post + $required;
        foreach ($post_info['return_reason'] as $language_id => $value) {
            if (!oc_validate_length($value['name'], 3, 128)) {
                $json['error']['name_' . $language_id] = $this->language->get('error_name');
            }
        }
        if (!$json) {
            // Return Reason
            $this->load->model('localisation/return_reason');
            if (!$post_info['return_reason_id']) {
                $json['return_reason_id'] = $this->model_localisation_return_reason->add_return_reason($post_info);
            } else {
                $this->model_localisation_return_reason->edit_return_reason($post_info['return_reason_id'], $post_info);
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
        $this->load->language('localisation/return_reason');
        $json = [];
        if (isset($this->request->post['selected'])) {
            $selected = (array) $this->request->post['selected'];
        } else {
            $selected = [];
        }
        if (!$this->user->has_permission('modify', 'localisation/return_reason')) {
            $json['error'] = $this->language->get('error_permission');
        }
        // Returns
        $this->load->model('sale/returns');
        // Total Returns
        foreach ($selected as $return_reason_id) {
            $return_total = $this->model_sale_returns->get_total_returns_by_return_reason_id($return_reason_id);
            if ($return_total) {
                $json['error'] = sprintf($this->language->get('error_return'), $return_total);
            }
        }
        if (!$json) {
            // Return Reason
            $this->load->model('localisation/return_reason');
            foreach ($selected as $return_reason_id) {
                $this->model_localisation_return_reason->delete_return_reason($return_reason_id);
            }
            $json['success'] = $this->language->get('text_success');
        }
        $this->response->add_header('Content-Type: application/json');
        $this->response->set_output(json_encode($json));
    }
}