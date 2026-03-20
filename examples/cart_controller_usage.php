<?php

declare(strict_types=1);

/**
 * Example: Interacting with the OpenCart cart via the Registry
 *
 * In OpenCart, all services are accessed through the Registry object.
 * Controllers and models access them as magic properties (e.g. $this->cart).
 *
 * This example shows common cart operations as they would appear inside a
 * Catalog Controller method.
 */

namespace Opencart\Catalog\Controller\Example;

class CartExample extends \Opencart\System\Engine\Controller
{
    /**
     * Example action showing typical cart interactions.
     *
     * Access URL (after enabling the route):
     *   /index.php?route=example/cart_example
     *
     * @return void
     */
    public function index(): void
    {
        // -----------------------------------------------------------------------
        // 1. Check if the cart has products
        // -----------------------------------------------------------------------
        if ($this->cart->has_products()) {
            $products = $this->cart->get_products();
            // $products is an array of product data arrays

            foreach ($products as $product) {
                // $product['name']     — product name (language-aware)
                // $product['quantity'] — quantity in cart
                // $product['price']    — formatted unit price string
                // $product['total']    — formatted line total string
                // $product['option']   — selected options array
            }
        }

        // -----------------------------------------------------------------------
        // 2. Add a product programmatically
        // -----------------------------------------------------------------------
        $product_id          = 42;
        $quantity            = 2;
        $options             = [];          // product_option_id => value
        $subscription_plan   = 0;           // 0 = no subscription

        $this->cart->add($product_id, $quantity, $options, $subscription_plan);

        // Clear cached shipping/payment selections after modifying the cart
        unset(
            $this->session->data['order_id'],
            $this->session->data['shipping_method'],
            $this->session->data['payment_method'],
        );

        // -----------------------------------------------------------------------
        // 3. Update an item quantity
        // -----------------------------------------------------------------------
        $cart_item_key = 1;   // The internal cart row key
        $this->cart->update($cart_item_key, 3);

        // -----------------------------------------------------------------------
        // 4. Remove an item
        // -----------------------------------------------------------------------
        $this->cart->remove($cart_item_key);

        // -----------------------------------------------------------------------
        // 5. Check stock and weight
        // -----------------------------------------------------------------------
        $in_stock = $this->cart->has_stock();
        $weight   = $this->cart->get_weight();

        // -----------------------------------------------------------------------
        // 6. Get tax totals (used for cart total display)
        // -----------------------------------------------------------------------
        $taxes = $this->cart->get_taxes();
        // $taxes is an array keyed by tax_class_id with tax amount values

        // -----------------------------------------------------------------------
        // 7. Render a JSON response
        // -----------------------------------------------------------------------
        $json = [
            'has_products' => $this->cart->has_products(),
            'product_count' => count($this->cart->get_products()),
            'in_stock'     => $in_stock,
        ];

        $this->response->add_header('Content-Type: application/json');
        $this->response->set_output(json_encode($json));
    }
}
