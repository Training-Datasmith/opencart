<?php

declare (strict_types=1);
namespace Opencart\System\Library\Cart;

/**
 * Class Cart
 *
 * @package Opencart\System\Library\Cart
 */
class Cart
{
    private object $db;
    private object $config;
    private object $customer;
    private object $session;
    private object $tax;
    private object $weight;
    /**
     * @var array<int, array<string, mixed>>
     */
    private array $data = [];
    /**
     * Bootstraps the cart library by resolving services from the registry
     * and populating the in-memory product data cache.
     *
     * On construction the library:
     * 1. Resolves db, config, customer, session, tax, and weight from the registry
     * 2. Purges expired anonymous cart rows older than `session_expire` seconds
     * 3. Migrates session-based cart items to the authenticated customer's account on login
     * 4. Pre-loads all cart product records into $this->data via get_products()
     *
     * @param \Opencart\System\Engine\Registry $registry Central service registry
     *
     * @see self::get_products() for the data structure returned
     */
    public function __construct(\Opencart\System\Engine\Registry $registry)
    {
        $this->db = $registry->get('db');
        $this->config = $registry->get('config');
        $this->customer = $registry->get('customer');
        $this->session = $registry->get('session');
        $this->tax = $registry->get('tax');
        $this->weight = $registry->get('weight');
        // Remove all the expired carts for visitors who never registered
        $this->db->query('DELETE FROM `' . DB_PREFIX . "cart` WHERE `store_id` = '" . (int) $this->config->get('config_store_id') . "' AND `customer_id` = '0' AND `date_added` < DATE_SUB(NOW(), INTERVAL " . (int) $this->config->get('session_expire') . ' SECOND)');
        if ($this->customer->is_logged()) {
            // We want to change the session ID on all the old items in the customers cart
            $this->db->query('UPDATE `' . DB_PREFIX . "cart` SET `session_id` = '" . $this->db->escape($this->session->get_id()) . "', `date_added` = NOW() WHERE `store_id` = '" . (int) $this->config->get('config_store_id') . "' AND `customer_id` = '" . (int) $this->customer->get_id() . "'");
            // Once the customer is logged in we want to update the customers cart
            $this->db->query('UPDATE `' . DB_PREFIX . "cart` SET `customer_id` = '" . (int) $this->customer->get_id() . "', `date_added` = NOW() WHERE `store_id` = '" . (int) $this->config->get('config_store_id') . "' AND `customer_id` = '0' AND `session_id` = '" . $this->db->escape($this->session->get_id()) . "'");
        }
        // Populate the cart data
        $this->data = $this->get_products();
    }
    /**
     * Returns all active cart product records for the current customer/session.
     *
     * Results are cached in $this->data after the first DB read. Each entry contains:
     * - product_id, cart_id, master_id, variant (array), override (array)
     * - name, model, image, price (formatted), total (formatted)
     * - quantity, minimum, minimum_status
     * - option (array of selected option values, including file uploads)
     * - subscription (subscription plan data or empty array)
     * - stock_status, stock (bool)
     * - shipping, shipping_class_id, tax_class_id, reward, points
     * - weight, weight_class_id, length, width, height, length_class_id
     *
     * @return array<int, array<string, mixed>> Cart product records, keyed sequentially
     *
     * @complexity O(n*o) where n = number of cart rows, o = average number of options per product
     * @see self::add() for adding products
     */
    public function get_products(): array
    {
        if (!$this->data) {
            $cart_query = $this->db->query('SELECT * FROM `' . DB_PREFIX . "cart` WHERE `store_id` = '" . (int) $this->config->get('config_store_id') . "' AND `customer_id` = '" . (int) $this->customer->get_id() . "' AND `session_id` = '" . $this->db->escape($this->session->get_id()) . "'");
            foreach ($cart_query->rows as $cart) {
                $stock_status = true;
                $product_query = $this->db->query('SELECT * FROM `' . DB_PREFIX . 'product_to_store` `p2s` LEFT JOIN `' . DB_PREFIX . 'product` `p` ON (`p2s`.`product_id` = `p`.`product_id`) LEFT JOIN `' . DB_PREFIX . "product_description` `pd` ON (`p`.`product_id` = `pd`.`product_id`) WHERE `p2s`.`store_id` = '" . (int) $this->config->get('config_store_id') . "' AND `p2s`.`product_id` = '" . (int) $cart['product_id'] . "' AND `pd`.`language_id` = '" . (int) $this->config->get('config_language_id') . "' AND `p`.`date_available` <= NOW() AND `p`.`status` = '1'");
                if ($product_query->num_rows && $cart['quantity'] > 0) {
                    $stock = $product_query->row['quantity'];
                    $option_price = 0;
                    $option_points = 0;
                    $option_weight = 0;
                    $option_data = [];
                    $product_options = (array) json_decode(!empty($cart['option']) ? $cart['option'] : '{}', true);
                    $variant = json_decode(!empty($product_query->row['variant']) ? $product_query->row['variant'] : '{}', true);
                    if ($variant) {
                        foreach ($variant as $key => $value) {
                            $product_options[$key] = $value;
                        }
                    }
                    if (!$product_query->row['master_id']) {
                        $product_id = $cart['product_id'];
                    } else {
                        $product_id = $product_query->row['master_id'];
                    }
                    foreach ($product_options as $product_option_id => $value) {
                        $option_query = $this->db->query('SELECT `po`.`product_option_id`, `po`.`option_id`, `od`.`name`, `o`.`type` FROM `' . DB_PREFIX . 'product_option` `po` LEFT JOIN `' . DB_PREFIX . 'option` `o` ON (`po`.`option_id` = `o`.`option_id`) LEFT JOIN `' . DB_PREFIX . "option_description` `od` ON (`o`.`option_id` = `od`.`option_id`) WHERE `po`.`product_option_id` = '" . (int) $product_option_id . "' AND `po`.`product_id` = '" . (int) $product_id . "' AND `od`.`language_id` = '" . (int) $this->config->get('config_language_id') . "'");
                        if ($option_query->num_rows) {
                            if ($option_query->row['type'] == 'select' || $option_query->row['type'] == 'radio') {
                                $option_value_query = $this->db->query('SELECT `pov`.`option_value_id`, `ovd`.`name`, `pov`.`quantity`, `pov`.`subtract`, `pov`.`price`, `pov`.`price_prefix`, `pov`.`points`, `pov`.`points_prefix`, `pov`.`weight`, `pov`.`weight_prefix` FROM `' . DB_PREFIX . 'product_option_value` `pov` LEFT JOIN `' . DB_PREFIX . 'option_value` `ov` ON (`pov`.`option_value_id` = `ov`.`option_value_id`) LEFT JOIN `' . DB_PREFIX . "option_value_description` `ovd` ON (`ov`.`option_value_id` = `ovd`.`option_value_id`) WHERE `pov`.`product_option_value_id` = '" . (int) $value . "' AND `pov`.`product_option_id` = '" . (int) $product_option_id . "' AND `ovd`.`language_id` = '" . (int) $this->config->get('config_language_id') . "'");
                                if ($option_value_query->num_rows) {
                                    if ($option_value_query->row['price_prefix'] == '+') {
                                        $option_price += $option_value_query->row['price'];
                                    } elseif ($option_value_query->row['price_prefix'] == '-') {
                                        $option_price -= $option_value_query->row['price'];
                                    }
                                    if ($option_value_query->row['points_prefix'] == '+') {
                                        $option_points += $option_value_query->row['points'];
                                    } elseif ($option_value_query->row['points_prefix'] == '-') {
                                        $option_points -= $option_value_query->row['points'];
                                    }
                                    if ($option_value_query->row['weight_prefix'] == '+') {
                                        $option_weight += $option_value_query->row['weight'];
                                    } elseif ($option_value_query->row['weight_prefix'] == '-') {
                                        $option_weight -= $option_value_query->row['weight'];
                                    }
                                    if ($option_value_query->row['subtract'] && (!$option_value_query->row['quantity'] || $option_value_query->row['quantity'] < $cart['quantity'])) {
                                        $stock_status = false;
                                    }
                                    $option_data[] = ['product_option_id' => $product_option_id, 'product_option_value_id' => $value, 'value' => $option_value_query->row['name']] + $option_query->row + $option_value_query->row;
                                }
                            } elseif ($option_query->row['type'] == 'checkbox' && is_array($value)) {
                                foreach ($value as $product_option_value_id) {
                                    $option_value_query = $this->db->query('SELECT `pov`.`option_value_id`, `pov`.`quantity`, `pov`.`subtract`, `pov`.`price`, `pov`.`price_prefix`, `pov`.`points`, `pov`.`points_prefix`, `pov`.`weight`, `pov`.`weight_prefix`, `ovd`.`name` FROM `' . DB_PREFIX . 'product_option_value` `pov` LEFT JOIN `' . DB_PREFIX . "option_value_description` `ovd` ON (`pov`.`option_value_id` = `ovd`.option_value_id) WHERE `pov`.product_option_value_id = '" . (int) $product_option_value_id . "' AND `pov`.product_option_id = '" . (int) $product_option_id . "' AND `ovd`.language_id = '" . (int) $this->config->get('config_language_id') . "'");
                                    if ($option_value_query->num_rows) {
                                        if ($option_value_query->row['price_prefix'] == '+') {
                                            $option_price += $option_value_query->row['price'];
                                        } elseif ($option_value_query->row['price_prefix'] == '-') {
                                            $option_price -= $option_value_query->row['price'];
                                        }
                                        if ($option_value_query->row['points_prefix'] == '+') {
                                            $option_points += $option_value_query->row['points'];
                                        } elseif ($option_value_query->row['points_prefix'] == '-') {
                                            $option_points -= $option_value_query->row['points'];
                                        }
                                        if ($option_value_query->row['weight_prefix'] == '+') {
                                            $option_weight += $option_value_query->row['weight'];
                                        } elseif ($option_value_query->row['weight_prefix'] == '-') {
                                            $option_weight -= $option_value_query->row['weight'];
                                        }
                                        if ($option_value_query->row['subtract'] && (!$option_value_query->row['quantity'] || $option_value_query->row['quantity'] < $cart['quantity'])) {
                                            $stock_status = false;
                                        }
                                        $option_data[] = ['product_option_id' => $product_option_id, 'product_option_value_id' => $product_option_value_id, 'value' => $option_value_query->row['name']] + $option_query->row + $option_value_query->row;
                                    }
                                }
                            } elseif ($option_query->row['type'] == 'text' || $option_query->row['type'] == 'textarea' || $option_query->row['type'] == 'file' || $option_query->row['type'] == 'date' || $option_query->row['type'] == 'datetime' || $option_query->row['type'] == 'time') {
                                $option_data[] = ['product_option_id' => $product_option_id, 'product_option_value_id' => 0, 'option_value_id' => 0, 'value' => $value, 'quantity' => 0, 'subtract' => 0, 'price' => 0, 'price_prefix' => '', 'points' => 0, 'points_prefix' => '', 'weight' => 0, 'weight_prefix' => ''] + $option_query->row;
                            }
                        }
                    }
                    // Get total products of the same product but with different options
                    $product_total = 0;
                    foreach ($cart_query->rows as $cart_2) {
                        if ($cart_2['product_id'] == $cart['product_id']) {
                            $product_total += $cart_2['quantity'];
                        }
                    }
                    $price = $product_query->row['price'] + $option_price;
                    $subscription_data = [];
                    $subscription_query = $this->db->query('SELECT * FROM `' . DB_PREFIX . 'product_subscription` `ps` LEFT JOIN `' . DB_PREFIX . 'subscription_plan` `sp` ON (`ps`.`subscription_plan_id` = `sp`.`subscription_plan_id`) LEFT JOIN `' . DB_PREFIX . "subscription_plan_description` `spd` ON (`sp`.`subscription_plan_id` = `spd`.`subscription_plan_id`) WHERE `ps`.`product_id` = '" . (int) $cart['product_id'] . "' AND `ps`.`subscription_plan_id` = '" . (int) $cart['subscription_plan_id'] . "' AND `ps`.`customer_group_id` = '" . (int) $this->config->get('config_customer_group_id') . "' AND `spd`.`language_id` = '" . (int) $this->config->get('config_language_id') . "' AND `sp`.`status` = '1'");
                    if ($subscription_query->num_rows) {
                        $subscription_data = ['remaining' => $subscription_query->row['duration']] + $subscription_query->row;
                        // Set the new price if is subscription product
                        $price = $subscription_query->row['price'];
                        if ($subscription_query->row['trial_status']) {
                            $price = $subscription_query->row['trial_price'];
                        }
                    }
                    // Product Discounts
                    $product_discount_query = $this->db->query('SELECT * FROM `' . DB_PREFIX . "product_discount` WHERE `product_id` = '" . (int) $cart['product_id'] . "' AND `customer_group_id` = '" . (int) $this->config->get('config_customer_group_id') . "' AND `quantity` <= '" . (int) $product_total . "' AND ((`date_start` = '0000-00-00' OR `date_start` < NOW()) AND (`date_end` = '0000-00-00' OR `date_end` > NOW())) ORDER BY `quantity` DESC, `priority` ASC, `price` ASC LIMIT 1");
                    if ($product_discount_query->num_rows) {
                        if ($product_discount_query->row['type'] == 'F') {
                            // Fixed Price
                            $price = $product_discount_query->row['price'] + $option_price;
                        } elseif ($product_discount_query->row['type'] == 'P') {
                            // Percentage
                            $price -= $price * ($product_discount_query->row['price'] / 100);
                        } elseif ($product_discount_query->row['type'] == 'S') {
                            // Subtract
                            $price -= $product_discount_query->row['price'];
                        }
                    }
                    // Stock
                    if (!$product_query->row['quantity'] || $product_query->row['quantity'] < $product_total) {
                        $stock_status = false;
                    }
                    // Minimum Quantity
                    if ($product_query->row['minimum'] > $product_total) {
                        $minimum = false;
                    } else {
                        $minimum = true;
                    }
                    // Reward Points
                    $product_reward_query = $this->db->query('SELECT `points` FROM `' . DB_PREFIX . "product_reward` WHERE `product_id` = '" . (int) $cart['product_id'] . "' AND `customer_group_id` = '" . (int) $this->config->get('config_customer_group_id') . "'");
                    if ($product_reward_query->num_rows) {
                        $reward = $product_reward_query->row['points'];
                    } else {
                        $reward = 0;
                    }
                    // Downloads
                    $download_data = [];
                    $download_query = $this->db->query('SELECT * FROM `' . DB_PREFIX . 'product_to_download` `p2d` LEFT JOIN `' . DB_PREFIX . 'download` `d` ON (`p2d`.`download_id` = `d`.`download_id`) LEFT JOIN `' . DB_PREFIX . "download_description` `dd` ON (`d`.`download_id` = `dd`.`download_id`) WHERE `p2d`.`product_id` = '" . (int) $cart['product_id'] . "' AND `dd`.`language_id` = '" . (int) $this->config->get('config_language_id') . "'");
                    foreach ($download_query->rows as $download) {
                        $download_data[] = $download;
                    }
                    $this->data[$cart['cart_id']] = ['cart_id' => $cart['cart_id'], 'option' => $option_data, 'subscription' => $subscription_data, 'download' => $download_data, 'quantity' => $cart['quantity'], 'minimum_status' => $minimum, 'stock' => $stock, 'stock_status' => $stock_status, 'price' => $price, 'total' => $price * $cart['quantity'], 'reward' => $reward * $cart['quantity'], 'points' => $product_query->row['points'] ? ($product_query->row['points'] + $option_points) * $cart['quantity'] : 0, 'weight' => ($product_query->row['weight'] + $option_weight) * $cart['quantity']] + $product_query->row;
                    // Use with order editor and subscriptions
                    if ($cart['override']) {
                        $override = json_decode($cart['override']);
                    } else {
                        $override = [];
                    }
                    foreach ($override as $key => $value) {
                        $this->data[$cart['cart_id']][$key] = $value;
                    }
                } else {
                    $this->remove($cart['cart_id']);
                }
            }
        }
        return $this->data;
    }
    /**
     * Adds a product to the cart, or increments its quantity if an identical row already exists.
     *
     * Matching is done by (store_id, customer_id/session_id, product_id, subscription_plan_id, option JSON).
     * If a matching row exists its quantity is incremented; otherwise a new row is inserted.
     * The in-memory data cache ($this->data) is cleared after the write so the next
     * call to get_products() re-queries the database.
     *
     * @param int                  $product_id           Primary key of the product record
     * @param int                  $quantity             Number of units to add (default 1)
     * @param array<string, mixed> $option               Selected product option values keyed by product_option_id
     * @param int                  $subscription_plan_id Primary key of the subscription plan (0 = none)
     * @param array<string, mixed> $override             Variant field overrides (populated from product variant data)
     *
     *
     * @example
     *
     * $this->cart->add($product_id, $quantity, $option, $subscription_plan_id, $override);
     */
    public function add(int $product_id, int $quantity = 1, array $option = [], int $subscription_plan_id = 0, array $override = []): void
    {
        $query = $this->db->query('SELECT COUNT(*) AS `total` FROM `' . DB_PREFIX . "cart` WHERE `store_id` = '" . (int) $this->config->get('config_store_id') . "' AND `customer_id` = '" . (int) $this->customer->get_id() . "' AND `session_id` = '" . $this->db->escape($this->session->get_id()) . "' AND `product_id` = '" . $product_id . "' AND `subscription_plan_id` = '" . $subscription_plan_id . "' AND `option` = '" . $this->db->escape(json_encode($option)) . "'");
        if (!$query->row['total']) {
            $this->db->query('INSERT INTO `' . DB_PREFIX . "cart` SET `store_id` = '" . (int) $this->config->get('config_store_id') . "', `customer_id` = '" . (int) $this->customer->get_id() . "', `session_id` = '" . $this->db->escape($this->session->get_id()) . "', `product_id` = '" . $product_id . "', `subscription_plan_id` = '" . $subscription_plan_id . "', `option` = '" . $this->db->escape(json_encode($option)) . "', `quantity` = '" . $quantity . "', `override` = '" . $this->db->escape(json_encode($override)) . "', `date_added` = NOW()");
        } else {
            $this->db->query('UPDATE `' . DB_PREFIX . 'cart` SET `quantity` = (`quantity` + ' . $quantity . ") WHERE `store_id` = '" . (int) $this->config->get('config_store_id') . "' AND `customer_id` = '" . (int) $this->customer->get_id() . "' AND `session_id` = '" . $this->db->escape($this->session->get_id()) . "' AND `product_id` = '" . $product_id . "' AND `subscription_plan_id` = '" . $subscription_plan_id . "' AND `option` = '" . $this->db->escape(json_encode($option)) . "'");
        }
        $this->data = [];
    }
    /**
     * Update
     *
     * @param int $cart_id  primary key of the cart record
     *
     *
     * @example
     *
     * $this->cart->update($cart_id, $quantity);
     */
    public function update(int $cart_id, int $quantity): void
    {
        $this->db->query('UPDATE `' . DB_PREFIX . "cart` SET `quantity` = '" . $quantity . "' WHERE `cart_id` = '" . $cart_id . "' AND `store_id` = '" . (int) $this->config->get('config_store_id') . "' AND `customer_id` = '" . (int) $this->customer->get_id() . "' AND `session_id` = '" . $this->db->escape($this->session->get_id()) . "'");
        $this->data = [];
    }
    /**
     * Has
     *
     * @param int $cart_id primary key of the cart record
     *
     *
     * @example
     *
     * $cart = $this->cart->has($cart_id);
     */
    public function has(int $cart_id): bool
    {
        return isset($this->data[$cart_id]);
    }
    /**
     * Remove
     *
     * @param int $cart_id primary key of the cart record
     *
     *
     * @example
     *
     * $cart = $this->cart->remove($cart_id);
     */
    public function remove(int $cart_id): void
    {
        $this->db->query('DELETE FROM `' . DB_PREFIX . "cart` WHERE `cart_id` = '" . $cart_id . "' AND `store_id` = '" . (int) $this->config->get('config_store_id') . "' AND `customer_id` = '" . (int) $this->customer->get_id() . "' AND `session_id` = '" . $this->db->escape($this->session->get_id()) . "'");
        unset($this->data[$cart_id]);
    }
    /**
     * Clear
     *
     *
     * @example
     *
     * $this->cart->clear();
     */
    public function clear(): void
    {
        $this->db->query('DELETE FROM `' . DB_PREFIX . "cart` WHERE `store_id` = '" . (int) $this->config->get('config_store_id') . "' AND `customer_id` = '" . (int) $this->customer->get_id() . "' AND `session_id` = '" . $this->db->escape($this->session->get_id()) . "'");
        $this->data = [];
    }
    /**
     * Get Subscriptions
     *
     * @return array<int, array<string, mixed>>
     *
     * @example
     *
     * $subscriptions = $this->cart->getSubscriptions();
     */
    public function get_subscriptions(): array
    {
        $product_data = [];
        foreach ($this->get_products() as $value) {
            if ($value['subscription']) {
                $product_data[] = $value;
            }
        }
        return $product_data;
    }
    /**
     * Get Weight
     *
     *
     * @example
     *
     * $weight = $this->cart->getWeight();
     */
    public function get_weight(): float
    {
        $weight = 0;
        foreach ($this->get_products() as $product) {
            if ($product['shipping']) {
                $weight += $this->weight->convert($product['weight'], $product['weight_class_id'], $this->config->get('config_weight_class_id'));
            }
        }
        return $weight;
    }
    /**
     * Get Sub Total
     *
     *
     * @example
     *
     * $sub_total = $this->cart->getSubTotal();
     */
    public function get_sub_total(): float
    {
        $total = 0;
        foreach ($this->get_products() as $product) {
            $total += $product['total'];
        }
        return $total;
    }
    /**
     * Get Taxes
     *
     * @return array<int, float>
     *
     * @example
     *
     * $taxes = $this->cart->getTaxes();
     */
    public function get_taxes(): array
    {
        $tax_data = [];
        foreach ($this->get_products() as $product) {
            if ($product['tax_class_id']) {
                $tax_rates = $this->tax->get_rates($product['price'], $product['tax_class_id']);
                foreach ($tax_rates as $tax_rate) {
                    if ($tax_rate['type'] == 'P') {
                        $quantity = $product['quantity'];
                    } else {
                        $quantity = 1;
                    }
                    if (!isset($tax_data[$tax_rate['tax_rate_id']])) {
                        $tax_data[$tax_rate['tax_rate_id']] = $tax_rate['amount'] * $quantity;
                    } else {
                        $tax_data[$tax_rate['tax_rate_id']] += $tax_rate['amount'] * $quantity;
                    }
                }
            }
        }
        return $tax_data;
    }
    /**
     * Get Total
     *
     *
     * @example
     *
     * $total = $this->cart->getTotal();
     */
    public function get_total(): float
    {
        $total = 0;
        foreach ($this->get_products() as $product) {
            $total += $this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity'];
        }
        return $total;
    }
    /**
     * Count Products
     *
     *
     * @example
     *
     * $count_products = $this->cart->countProducts();
     */
    public function count_products(): int
    {
        $product_total = 0;
        $products = $this->get_products();
        foreach ($products as $product) {
            $product_total += $product['quantity'];
        }
        return $product_total;
    }
    /**
     * Returns whether the cart contains at least one product.
     *
     * @return bool True when get_products() returns a non-empty array
     */
    public function has_products(): bool
    {
        return (bool) count($this->get_products());
    }

    /**
     * Returns whether the cart contains at least one subscription product.
     *
     * @return bool True when get_subscriptions() returns a non-empty array
     */
    public function has_subscription(): bool
    {
        return (bool) count($this->get_subscriptions());
    }

    /**
     * Returns whether all products in the cart are in stock.
     *
     * Iterates all cart products and returns false as soon as any product has a
     * falsy stock_status value. Returns true for empty carts.
     *
     * @return bool True when every product has stock_status = true (or cart is empty)
     *
     * @complexity O(n) where n is the number of cart products
     */
    public function has_stock(): bool
    {
        foreach ($this->get_products() as $product) {
            if (!$product['stock_status']) {
                return false;
            }
        }
        return true;
    }
    /**
     * Has Minimum
     *
     * Check if any products have a minimum order quantity amount and do not meet the requirement
     *
     *
     * @example
     *
     * $cart = $this->cart->hasMinimum();
     */
    public function has_minimum(): bool
    {
        foreach ($this->get_products() as $product) {
            if (!$product['minimum_status']) {
                return false;
            }
        }
        return true;
    }
    /**
     * Has Shipping
     *
     *
     * @example
     *
     * $cart = $this->cart->hasShipping();
     */
    public function has_shipping(): bool
    {
        foreach ($this->get_products() as $product) {
            if ($product['shipping']) {
                return true;
            }
        }
        return false;
    }
    /**
     * Has Download
     *
     *
     * @example
     *
     * $cart = $this->cart->hasDownload();
     */
    public function has_download(): bool
    {
        foreach ($this->get_products() as $product) {
            if ($product['download']) {
                return true;
            }
        }
        return false;
    }
}