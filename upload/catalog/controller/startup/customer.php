<?php

declare (strict_types=1);
namespace Opencart\Catalog\Controller\Startup;

/**
 * Class Customer
 *
 * @package Opencart\Catalog\Controller\Startup
 */
class Customer extends \Opencart\System\Engine\Controller
{
    /**
     * Index
     */
    public function index(): void
    {
        $this->registry->set('customer', new \Opencart\System\Library\Cart\Customer($this->registry));
        // Customer Group
        if (isset($this->session->data['customer'])) {
            $this->config->set('config_customer_group_id', $this->session->data['customer']['customer_group_id']);
        } elseif ($this->customer->is_logged()) {
            // Logged in customers
            $this->config->set('config_customer_group_id', $this->customer->get_group_id());
        }
    }
}