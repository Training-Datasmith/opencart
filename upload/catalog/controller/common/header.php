<?php

declare (strict_types=1);
namespace Opencart\Catalog\Controller\Common;

/**
 * Class Header
 *
 * Can be called from $this->load->controller('common/header');
 *
 * @package Opencart\Catalog\Controller\Common
 */
class Header extends \Opencart\System\Engine\Controller
{
    /**
     * Index
     */
    public function index(): string
    {
        $data['lang'] = $this->language->get('code');
        $data['direction'] = $this->language->get('direction');
        $data['title'] = $this->document->get_title();
        $data['base'] = $this->config->get('config_url');
        $data['description'] = $this->document->get_description();
        $data['keywords'] = $this->document->get_keywords();
        $data['styles'] = $this->document->get_styles();
        $data['links'] = $this->document->get_links();
        $data['scripts'] = $this->document->get_scripts();
        $data['name'] = $this->config->get('config_name');
        // Fav icon
        if (is_file(DIR_IMAGE . $this->config->get('config_icon'))) {
            $data['icon'] = $this->config->get('config_url') . 'image/' . $this->config->get('config_icon');
        } else {
            $data['icon'] = '';
        }
        if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
            $data['logo'] = $this->config->get('config_url') . 'image/' . $this->config->get('config_logo');
        } else {
            $data['logo'] = '';
        }
        $this->load->language('common/header');
        // Wishlist
        if ($this->customer->is_logged()) {
            $this->load->model('account/wishlist');
            $data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), $this->model_account_wishlist->get_total_wishlist($this->customer->get_id()));
        } else {
            $data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0);
        }
        $data['home'] = $this->url->link('common/home', 'language=' . $this->config->get('config_language'));
        $data['wishlist'] = $this->url->link('account/wishlist', 'language=' . $this->config->get('config_language') . (isset($this->session->data['customer_token']) ? '&customer_token=' . $this->session->data['customer_token'] : ''));
        $data['logged'] = $this->customer->is_logged();
        if (!$this->customer->is_logged()) {
            $data['register'] = $this->url->link('account/register', 'language=' . $this->config->get('config_language'));
            $data['login'] = $this->url->link('account/login', 'language=' . $this->config->get('config_language'));
        } else {
            $data['account'] = $this->url->link('account/account', 'language=' . $this->config->get('config_language') . '&customer_token=' . $this->session->data['customer_token']);
            $data['order'] = $this->url->link('account/order', 'language=' . $this->config->get('config_language') . '&customer_token=' . $this->session->data['customer_token']);
            $data['transaction'] = $this->url->link('account/transaction', 'language=' . $this->config->get('config_language') . '&customer_token=' . $this->session->data['customer_token']);
            $data['download'] = $this->url->link('account/download', 'language=' . $this->config->get('config_language') . '&customer_token=' . $this->session->data['customer_token']);
            $data['logout'] = $this->url->link('account/logout', 'language=' . $this->config->get('config_language'));
        }
        $data['shopping_cart'] = $this->url->link('checkout/cart', 'language=' . $this->config->get('config_language'));
        $data['checkout'] = $this->url->link('checkout/checkout', 'language=' . $this->config->get('config_language'));
        $data['contact'] = $this->url->link('information/contact', 'language=' . $this->config->get('config_language'));
        $data['telephone'] = $this->config->get('config_telephone');
        $data['language'] = $this->load->controller('common/language');
        $data['currency'] = $this->load->controller('common/currency');
        $data['search'] = $this->load->controller('common/search');
        $data['cart'] = $this->load->controller('common/cart');
        $data['menu'] = $this->load->controller('common/menu');
        return $this->load->view('common/header', $data);
    }
}