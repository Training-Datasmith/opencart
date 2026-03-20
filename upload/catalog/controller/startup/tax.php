<?php

declare (strict_types=1);
namespace Opencart\Catalog\Controller\Startup;

/**
 * Class Tax
 *
 * @package Opencart\Catalog\Controller\Startup
 */
class Tax extends \Opencart\System\Engine\Controller
{
    /**
     * Index
     */
    public function index(): void
    {
        $this->registry->set('tax', new \Opencart\System\Library\Cart\Tax($this->registry));
        if (isset($this->session->data['shipping_address'])) {
            $this->tax->set_shipping_address((int) $this->session->data['shipping_address']['country_id'], (int) $this->session->data['shipping_address']['zone_id']);
        } elseif ($this->config->get('config_tax_default') == 'shipping') {
            $this->tax->set_shipping_address((int) $this->config->get('config_country_id'), (int) $this->config->get('config_zone_id'));
        }
        if (isset($this->session->data['payment_address'])) {
            $this->tax->set_payment_address((int) $this->session->data['payment_address']['country_id'], (int) $this->session->data['payment_address']['zone_id']);
        } elseif ($this->config->get('config_tax_default') == 'payment') {
            $this->tax->set_payment_address((int) $this->config->get('config_country_id'), (int) $this->config->get('config_zone_id'));
        }
        $this->tax->set_store_address((int) $this->config->get('config_country_id'), (int) $this->config->get('config_zone_id'));
    }
}