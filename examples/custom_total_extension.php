<?php

declare(strict_types=1);

/**
 * Example: Custom cart total extension for OpenCart 4.x
 *
 * Total extensions add custom line items (fees, discounts, custom taxes) to the
 * cart total display. They are loaded automatically when the extension is enabled
 * and the code is registered under the "total" extension type.
 *
 * File path in extension:
 *   extension/handling_fee/catalog/controller/checkout/handling_fee.php
 *
 * Corresponding model:
 *   extension/handling_fee/catalog/model/checkout/handling_fee.php
 *
 * Register the extension in the admin panel under Extensions > Totals.
 */

namespace Opencart\Extension\HandlingFee\Catalog\Controller\Checkout;

/**
 * Adds a fixed handling fee to the cart when order total is below a threshold.
 *
 * Configuration keys (set in admin):
 *   - total_handling_fee_status  (bool)
 *   - total_handling_fee_fee     (float) — fee amount in store currency
 *   - total_handling_fee_threshold (float) — order total below which fee applies
 *   - total_handling_fee_sort_order (int)
 */
class HandlingFee extends \Opencart\System\Engine\Controller
{
    /**
     * Appends the handling fee line to the totals array if conditions are met.
     *
     * This method is called by the checkout cart model's get_totals() closure.
     * It receives the totals array and current order total by reference.
     *
     * @param array<int, array<string, mixed>> &$totals Reference to the cart totals array
     * @param array<int, float>                &$taxes  Reference to accumulated tax amounts
     * @param float                            &$total  Reference to the running order grand total
     *
     * @return void Modifies $totals and $total by reference
     */
    public function index(array &$totals, array &$taxes, float &$total): void
    {
        $this->load->language('extension/handling_fee/checkout/handling_fee');

        if (!$this->config->get('total_handling_fee_status')) {
            return;
        }

        $threshold = (float) $this->config->get('total_handling_fee_threshold');
        $fee       = (float) $this->config->get('total_handling_fee_fee');

        if ($total < $threshold && $fee > 0.0) {
            $totals[] = [
                'extension'  => 'handling_fee',
                'code'       => 'handling_fee',
                'title'      => $this->language->get('text_handling_fee'),
                'value'      => $fee,
                'sort_order' => (int) $this->config->get('total_handling_fee_sort_order'),
            ];

            $total += $fee;
        }
    }
}
