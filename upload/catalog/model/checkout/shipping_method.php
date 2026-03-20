<?php

declare (strict_types=1);
namespace Opencart\Catalog\Model\Checkout;

/**
 * Class Shipping Method
 *
 * Can be called using $this->load->model('checkout/shipping_method');
 *
 * @package Opencart\Catalog\Model\Checkout
 */
class Shipping_Method extends \Opencart\System\Engine\Model
{
    /**
     * Get Methods
     *
     * @param array<string, mixed> $shipping_address array of data
     *
     * @return array<string, array<string, mixed>>
     */
    public function get_methods(array $shipping_address): array
    {
        $method_data = [];
        // Extensions
        $this->load->model('setting/extension');
        $results = $this->model_setting_extension->get_extensions_by_type('shipping');
        foreach ($results as $result) {
            if ($this->config->get('shipping_' . $result['code'] . '_status')) {
                $this->load->model('extension/' . $result['extension'] . '/shipping/' . $result['code']);
                $key = 'model_extension_' . $result['extension'] . '_shipping_' . $result['code'];
                if (isset($this->{$key}->get_quote)) {
                    $quote = $this->{$key}->get_quote($shipping_address);
                    if ($quote) {
                        $method_data[$result['code']] = $quote;
                    }
                }
            }
        }
        $sort_order = [];
        foreach ($method_data as $key => $value) {
            $sort_order[$key] = $value['sort_order'];
        }
        array_multisort($sort_order, SORT_ASC, $method_data);
        return $method_data;
    }
}