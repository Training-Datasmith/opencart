<?php

declare (strict_types=1);
namespace Opencart\Catalog\Controller\Checkout;

/**
 * Class Checkout
 *
 * @package Opencart\Catalog\Controller\Checkout
 */
class Checkout extends \Opencart\System\Engine\Controller
{
    /**
     * Index
     */
    public function index(): void
    {
        // Validate cart to see if it has products and has stock.
        if (!$this->cart->has_products() || !$this->cart->has_stock() && !$this->config->get('config_stock_checkout') || !$this->cart->has_minimum()) {
            $this->response->redirect($this->url->link('checkout/cart', 'language=' . $this->config->get('config_language'), true));
        }
        $this->load->language('checkout/checkout');
        $this->document->set_title($this->language->get('heading_title'));
        $this->document->add_script('catalog/view/javascript/checkout.js');
        $data['breadcrumbs'] = [];
        $data['breadcrumbs'][] = ['text' => $this->language->get('text_home'), 'href' => $this->url->link('common/home', 'language=' . $this->config->get('config_language'))];
        $data['breadcrumbs'][] = ['text' => $this->language->get('text_cart'), 'href' => $this->url->link('checkout/cart', 'language=' . $this->config->get('config_language'))];
        $data['breadcrumbs'][] = ['text' => $this->language->get('heading_title'), 'href' => $this->url->link('checkout/checkout', 'language=' . $this->config->get('config_language'))];
        if (!$this->customer->is_logged()) {
            $data['register'] = $this->load->controller('checkout/register');
        } else {
            $data['register'] = '';
        }
        if ($this->customer->is_logged() && $this->config->get('config_checkout_payment_address')) {
            $data['payment_address'] = $this->load->controller('checkout/payment_address');
        } else {
            $data['payment_address'] = '';
        }
        if ($this->customer->is_logged() && $this->cart->has_shipping()) {
            $data['shipping_address'] = $this->load->controller('checkout/shipping_address');
        } else {
            $data['shipping_address'] = '';
        }
        if ($this->cart->has_shipping()) {
            $data['shipping_method'] = $this->load->controller('checkout/shipping_method');
        } else {
            $data['shipping_method'] = '';
        }
        $data['payment_method'] = $this->load->controller('checkout/payment_method');
        $data['confirm'] = $this->load->controller('checkout/confirm');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');
        $this->response->set_output($this->load->view('checkout/checkout', $data));
    }
}