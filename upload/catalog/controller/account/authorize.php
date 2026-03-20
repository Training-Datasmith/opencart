<?php

declare (strict_types=1);
namespace Opencart\Catalog\Controller\Account;

/**
 * Class Authorize
 *
 * @package Opencart\Catalog\Controller\Account
 */
class Authorize extends \Opencart\System\Engine\Controller
{
    /**
     * Index
     */
    public function index(): void
    {
        $this->load->language('account/authorize');
        if (isset($this->request->cookie['customer_authorize'])) {
            $token = $this->request->cookie['customer_authorize'];
        } else {
            $token = '';
        }
        // Make sure the customer is logged in.
        if (!$this->customer->is_logged()) {
            $this->response->redirect($this->url->link('account/login', 'language=' . $this->config->get('config_language'), true));
        }
        // Check total attempts
        $this->load->model('account/customer');
        $token_info = $this->model_account_customer->get_authorize_by_token($this->customer->get_id(), $token);
        if ($token_info && $token_info['total'] > 2) {
            $this->response->redirect($this->url->link('account/authorize.reset', 'language=' . $this->config->get('config_language'), true));
        }
        $this->document->set_title($this->language->get('heading_title'));
        $this->document->add_script('catalog/view/javascript/authorize.js');
        $data['action'] = $this->url->link('account/authorize.save', 'language=' . $this->config->get('config_language'));
        if (!$token_info) {
            // Create a token that can be stored as a cookie and will be used to identify device is safe.
            $token = oc_token(32);
            $authorize_data = ['token' => $token, 'ip' => oc_get_ip(), 'user_agent' => $this->request->server['HTTP_USER_AGENT']];
            $this->model_account_customer->add_authorize($this->customer->get_id(), $authorize_data);
            setcookie('customer_authorize', $token, ['expires' => time() + 60 * 60 * 24 * 90]);
        }
        // Set the code to be emailed
        $this->session->data['code'] = oc_token(6);
        if (isset($this->request->get['route']) && !str_starts_with($this->request->get['route'], 'account/authorize')) {
            $args = $this->request->get;
            $route = $args['route'];
            unset($args['route']);
            unset($args['customer_token']);
            $url = '';
            if ($args) {
                $url .= http_build_query($args);
            }
            $data['redirect'] = $this->url->link($route, $url);
        } else {
            $data['redirect'] = '';
        }
        $data['language'] = $this->config->get('config_language');
        $data['header'] = $this->load->controller('common/header');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->set_output($this->load->view('account/authorize', $data));
    }
    /**
     * Send
     */
    public function send(): void
    {
        $this->load->language('account/authorize');
        $json = [];
        if (isset($this->request->cookie['customer_authorize'])) {
            $token = $this->request->cookie['customer_authorize'];
        } else {
            $token = '';
        }
        // 1. Making sure the customer is logged in.
        if ($this->customer->is_logged()) {
            // 2. If token already exists check its valid
            $this->load->model('account/customer');
            $token_info = $this->model_account_customer->get_authorize_by_token($this->customer->get_id(), $token);
            if (!$token_info) {
                $json['redirect'] = $this->url->link('account/authorize', 'language=' . $this->config->get('config_language'), true);
                // If token is valid and total attempts are more than 2, redirect to unlock page.
            } elseif ($token_info['total'] > 2) {
                $json['redirect'] = $this->url->link('account/authorize.reset', 'language=' . $this->config->get('config_language'), true);
            }
        } else {
            $json['redirect'] = $this->url->link('account/login', 'language=' . $this->config->get('config_language'), true);
        }
        if (!$json) {
            // Set the code to be emailed
            $this->session->data['code'] = oc_token(6);
            $json['success'] = $this->language->get('text_sent');
        }
        $this->response->add_header('Content-Type: application/json');
        $this->response->set_output(json_encode($json));
    }
    /**
     * Save
     */
    public function save(): void
    {
        $this->load->language('account/authorize');
        $json = [];
        $required = ['code' => '', 'redirect' => ''];
        $post_info = $this->request->post + $required;
        if (isset($this->request->cookie['customer_authorize'])) {
            $token = $this->request->cookie['customer_authorize'];
        } else {
            $token = '';
        }
        // Make sure the customer is logged in.
        if ($this->customer->is_logged()) {
            // If token already exists check its valid
            $this->load->model('account/customer');
            $token_info = $this->model_account_customer->get_authorize_by_token($this->customer->get_id(), $token);
            if (!$token_info) {
                $json['redirect'] = $this->url->link('account/authorize', 'language=' . $this->config->get('config_language'), true);
            } elseif ($token_info['total'] > 2) {
                $json['redirect'] = $this->url->link('account/authorize.reset', 'language=' . $this->config->get('config_language'), true);
            } elseif (!isset($post_info['code']) || !isset($this->session->data['code']) || $post_info['code'] != $this->session->data['code']) {
                $total = $token_info['total'] + 1;
                if ($total <= 2) {
                    $json['error'] = $this->language->get('error_code');
                } else {
                    unset($this->session->data['code']);
                    $json['redirect'] = $this->url->link('account/authorize.reset', 'language=' . $this->config->get('config_language'), true);
                }
                $this->model_account_customer->edit_authorize_total($token_info['customer_authorize_id'], $total);
            }
        } else {
            $json['redirect'] = $this->url->link('account/login', 'language=' . $this->config->get('config_language'), true);
        }
        if (!$json) {
            unset($this->session->data['code']);
            // On success we need to reset the attempts and status.
            $this->model_account_customer->edit_authorize_status($token_info['customer_authorize_id'], true);
            $this->model_account_customer->edit_authorize_total($token_info['customer_authorize_id'], 0);
            if (isset($post_info['redirect'])) {
                $redirect = urldecode(html_entity_decode($post_info['redirect'], ENT_QUOTES, 'UTF-8'));
            } else {
                $redirect = '';
            }
            // Register the cookie for security.
            if ($redirect && str_starts_with($redirect, $this->config->get('config_url'))) {
                $json['redirect'] = $redirect . '&customer_token=' . $this->session->data['customer_token'];
            } else {
                $json['redirect'] = $this->url->link('account/account', 'customer_token=' . $this->session->data['customer_token'], true);
            }
        }
        $this->response->add_header('Content-Type: application/json');
        $this->response->set_output(json_encode($json));
    }
    /**
     * Unlock
     */
    public function reset(): void
    {
        $this->load->language('account/authorize');
        if (isset($this->request->cookie['customer_authorize'])) {
            $token = $this->request->cookie['customer_authorize'];
        } else {
            $token = '';
        }
        if (!$this->customer->is_logged()) {
            $this->response->redirect($this->url->link('account/login', 'language=' . $this->config->get('config_language'), true));
        }
        // Check total attempts
        $this->load->model('account/customer');
        $token_info = $this->model_account_customer->get_authorize_by_token($this->customer->get_id(), $token);
        if (!$token_info || $token_info['total'] <= 2) {
            // Redirect if already have a valid token.
            $this->response->redirect($this->url->link('account/authorize', 'language=' . $this->config->get('config_language'), true));
        }
        $this->document->set_title($this->language->get('heading_title'));
        $this->document->add_script('catalog/view/javascript/authorize.js');
        $data['language'] = $this->config->get('config_language');
        $data['header'] = $this->load->controller('common/header');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->set_output($this->load->view('account/authorize_reset', $data));
    }
    /**
     * Confirm
     */
    public function confirm(): void
    {
        $this->load->language('account/authorize');
        $json = [];
        if (isset($this->request->cookie['customer_authorize'])) {
            $token = $this->request->cookie['customer_authorize'];
        } else {
            $token = '';
        }
        if ($this->customer->is_logged()) {
            // Check total attempts
            $this->load->model('account/customer');
            $token_info = $this->model_account_customer->get_authorize_by_token($this->customer->get_id(), $token);
            if (!$token_info || $token_info['total'] <= 2) {
                $json['redirect'] = $this->url->link('account/authorize', 'language=' . $this->config->get('config_language'), true);
            }
        } else {
            $json['redirect'] = $this->url->link('account/login', 'language=' . $this->config->get('config_language'), true);
        }
        if (!$json) {
            // Create reset code
            $this->model_account_customer->add_token($this->customer->get_id(), 'authorize', oc_token(32));
            $json['success'] = $this->language->get('text_link');
        }
        $this->response->add_header('Content-Type: application/json');
        $this->response->set_output(json_encode($json));
    }
    /**
     * Reset
     *
     * We have to keep the reset method from blocking requests because some email clients will block cross site requests.
     */
    public function unlock(): void
    {
        $this->load->language('account/authorize');
        if (isset($this->request->get['email'])) {
            $email = urldecode((string) $this->request->get['email']);
        } else {
            $email = '';
        }
        if (isset($this->request->get['code'])) {
            $code = (string) $this->request->get['code'];
        } else {
            $code = '';
        }
        $this->document->set_title($this->language->get('heading_title'));
        // Check total attempts
        $this->load->model('account/customer');
        $customer_info = $this->model_account_customer->get_token_by_code($code);
        if ($customer_info && $customer_info['email'] == $email) {
            $data['text_unlock'] = $this->language->get('text_unlock');
            $this->model_account_customer->reset_authorizes($customer_info['customer_id']);
        } else {
            $data['text_unlock'] = $this->language->get('text_failed');
        }
        // Reset token so it can't be used again
        $this->model_account_customer->delete_token_by_code($code);
        // Logout customer
        $this->customer->logout();
        unset($this->session->data['order_id']);
        unset($this->session->data['customer']);
        unset($this->session->data['shipping_address']);
        unset($this->session->data['shipping_method']);
        unset($this->session->data['shipping_methods']);
        unset($this->session->data['payment_address']);
        unset($this->session->data['payment_method']);
        unset($this->session->data['payment_methods']);
        unset($this->session->data['comment']);
        unset($this->session->data['coupon']);
        unset($this->session->data['reward']);
        // Remove customer token if set
        unset($this->session->data['customer_token']);
        $data['login'] = $this->url->link('account/login', 'language=' . $this->config->get('config_language'));
        $data['header'] = $this->load->controller('common/header');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->set_output($this->load->view('account/authorize_unlock', $data));
    }
}