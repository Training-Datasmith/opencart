<?php

declare (strict_types=1);
namespace Opencart\Catalog\Controller\Account;

/**
 * Class Custom Field
 *
 * @package Opencart\Catalog\Controller\Account
 */
class Custom_Field extends \Opencart\System\Engine\Controller
{
    /**
     * Index
     */
    public function index(): void
    {
        // Customer Group
        if (isset($this->request->get['customer_group_id']) && in_array((int) $this->request->get['customer_group_id'], (array) $this->config->get('config_customer_group_list'))) {
            $customer_group_id = (int) $this->request->get['customer_group_id'];
        } else {
            $customer_group_id = (int) $this->config->get('config_customer_group_id');
        }
        $this->load->model('account/custom_field');
        $this->response->add_header('Content-Type: application/json');
        $this->response->set_output(json_encode($this->model_account_custom_field->get_custom_fields($customer_group_id)));
    }
}