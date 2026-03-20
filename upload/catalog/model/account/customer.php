<?php

declare (strict_types=1);
namespace Opencart\Catalog\Model\Account;

/**
 * Class Customer
 *
 * Can be called using $this->load->model('account/customer');
 *
 * @package Opencart\Catalog\Model\Account
 */
class Customer extends \Opencart\System\Engine\Model
{
    /**
     * Add Customer
     *
     * Create a new customer record in the database.
     *
     * @param array<string, mixed> $data array of data
     *
     * @return int returns the primary key of the new customer record
     *
     * @example
     *
     * $customer_data = [
     *     'store_id'     => 1,
     *     'language_id'  => 1,
     *     'firstname'    => 'John',
     *     'lastname'     => 'Doe',
     *     'email'        => 'demo@opencart.com',
     *     'telephone'    => '1234567890',
     *     'custom_field' => [],
     *     'password'     => '',
     *     'newsletter'   => 0,
     *     'ip'           => '',
     *     'status'       => 0
     * ];
     *
     * $this->load->model('account/customer');
     *
     * $this->model_account_customer_customer->addCustomer($customer_data);
     */
    public function add_customer(array $data): int
    {
        if (isset($data['customer_group_id']) && is_array($this->config->get('config_customer_group_list')) && in_array($data['customer_group_id'], $this->config->get('config_customer_group_list'))) {
            $customer_group_id = (int) $data['customer_group_id'];
        } else {
            $customer_group_id = (int) $this->config->get('config_customer_group_id');
        }
        // Customer Group
        $this->load->model('account/customer_group');
        $customer_group_info = $this->model_account_customer_group->get_customer_group($customer_group_id);
        $this->db->query('INSERT INTO `' . DB_PREFIX . "customer` SET `customer_group_id` = '" . $customer_group_id . "', `store_id` = '" . (int) $this->config->get('config_store_id') . "', `language_id` = '" . (int) $this->config->get('config_language_id') . "', `firstname` = '" . $this->db->escape($data['firstname']) . "', `lastname` = '" . $this->db->escape($data['lastname']) . "', `email` = '" . $this->db->escape(oc_strtolower($data['email'])) . "', `telephone` = '" . $this->db->escape($data['telephone']) . "', `custom_field` = '" . $this->db->escape(isset($data['custom_field']) ? json_encode($data['custom_field']) : '') . "', `password` = '" . $this->db->escape(password_hash(html_entity_decode($data['password'], ENT_QUOTES, 'UTF-8'), PASSWORD_DEFAULT)) . "', `newsletter` = '" . (isset($data['newsletter']) ? (int) $data['newsletter'] : 0) . "', `ip` = '" . $this->db->escape(oc_get_ip()) . "', `status` = '" . (int) !$customer_group_info['approval'] . "', `date_added` = NOW()");
        $customer_id = $this->db->get_last_id();
        // Approval
        if ($customer_group_info['approval']) {
            $this->load->model('account/approval');
            $this->model_account_approval->add_approval($customer_id, 'customer');
        }
        return $customer_id;
    }
    /**
     * Edit Customer
     *
     * Edit customer record in the database.
     *
     * @param int                  $customer_id primary key of the customer record
     * @param array<string, mixed> $data        array of data
     *
     *
     * @example
     *
     * $customer_data = [
     *     'firstname'    => 'John',
     *     'lastname'     => 'Doe',
     *     'email'        => 'demo@opencart.com',
     *     'telephone'    => '123467890',
     *     'custom_field' => []
     * ];
     *
     * $this->load->model('account/customer');
     *
     * $this->model_account_customer_customer->editCustomer($customer_id, $customer_data);
     */
    public function edit_customer(int $customer_id, array $data): void
    {
        $this->db->query('UPDATE `' . DB_PREFIX . "customer` SET `firstname` = '" . $this->db->escape($data['firstname']) . "', `lastname` = '" . $this->db->escape($data['lastname']) . "', `email` = '" . $this->db->escape(oc_strtolower($data['email'])) . "', `telephone` = '" . $this->db->escape($data['telephone']) . "', `custom_field` = '" . $this->db->escape(isset($data['custom_field']) ? json_encode($data['custom_field']) : '') . "' WHERE `customer_id` = '" . $customer_id . "'");
    }
    /**
     * Edit Password
     *
     *
     *
     * @example
     *
     * $this->load->model('account/customer');
     *
     * $this->model_account_customer->editPassword($email, $password);
     */
    public function edit_password(string $email, string $password): void
    {
        $this->db->query('UPDATE `' . DB_PREFIX . "customer` SET `password` = '" . $this->db->escape(password_hash(html_entity_decode($password, ENT_QUOTES, 'UTF-8'), PASSWORD_DEFAULT)) . "' WHERE LCASE(`email`) = '" . $this->db->escape(oc_strtolower($email)) . "'");
    }
    /**
     * Edit Newsletter
     *
     * Edit customer newsletter record in the database.
     *
     * @param int  $customer_id primary key of the customer record
     *
     *
     * @example
     *
     * $this->load->model('account/customer');
     *
     * $this->model_account_customer->editNewsletter($customer_id, $newsletter);
     */
    public function edit_newsletter(int $customer_id, bool $newsletter): void
    {
        $this->db->query('UPDATE `' . DB_PREFIX . "customer` SET `newsletter` = '" . $newsletter . "' WHERE `customer_id` = '" . $customer_id . "'");
    }
    /**
     * Delete Customer
     *
     * Delete customer record in the database.
     *
     * @param int $customer_id primary key of the customer record
     *
     *
     * @example
     *
     * $this->load->model('account/customer');
     *
     * $this->model_account_customer->deleteCustomer($customer_id);
     */
    public function delete_customer(int $customer_id): void
    {
        $this->db->query('DELETE FROM `' . DB_PREFIX . "customer` WHERE `customer_id` = '" . $customer_id . "'");
        // Activities
        $this->load->model('account/activity');
        $this->model_account_activity->delete_activities($customer_id);
        // Addresses
        $this->load->model('account/address');
        $this->model_account_address->delete_addresses($customer_id);
        // Affiliate
        $this->load->model('account/affiliate');
        $this->model_account_affiliate->delete_affiliate($customer_id);
        // Customer Approvals
        $this->load->model('account/approval');
        $this->model_account_approval->delete_approvals($customer_id);
        // Rewards
        $this->load->model('account/reward');
        $this->model_account_reward->delete_rewards($customer_id);
        // Transactions
        $this->load->model('account/transaction');
        $this->model_account_transaction->delete_transactions($customer_id);
        // Wishlists
        $this->load->model('account/wishlist');
        $this->model_account_wishlist->delete_wishlists($customer_id);
        $this->delete_histories($customer_id);
        $this->delete_ips($customer_id);
        $this->delete_authorizes($customer_id);
    }
    /**
     * Get Customer
     *
     * Get the record of the customer record in the database.
     *
     * @param int $customer_id primary key of the customer record
     *
     * @return array<string, mixed> customer record that has the customer ID
     *
     * @example
     *
     * $this->load->model('account/customer');
     *
     * $customer_info = $this->model_account_customer->getCustomer($customer_id);
     */
    public function get_customer(int $customer_id): array
    {
        $query = $this->db->query('SELECT * FROM `' . DB_PREFIX . "customer` WHERE `customer_id` = '" . $customer_id . "'");
        if ($query->num_rows) {
            return ['custom_field' => $query->row['custom_field'] ? json_decode($query->row['custom_field'], true) : []] + $query->row;
        }
        return [];
    }
    /**
     * Get Customer By Email
     *
     *
     * @return array<string, mixed>
     * @example
     *
     * $this->load->model('account/customer');
     *
     * $customer_info = $this->model_account_customer->getCustomerByEmail($email);
     */
    public function get_customer_by_email(string $email): array
    {
        $query = $this->db->query('SELECT * FROM `' . DB_PREFIX . "customer` WHERE LCASE(`email`) = '" . $this->db->escape(oc_strtolower($email)) . "'");
        if ($query->num_rows) {
            return ['custom_field' => $query->row['custom_field'] ? json_decode($query->row['custom_field'], true) : []] + $query->row;
        }
        return [];
    }
    /**
     * Get Total Customers By Email
     *
     *
     *
     * @example
     *
     * $this->load->model('account/customer');
     *
     * $customer_info = $this->model_account_customer->getTotalCustomersByEmail($email);
     */
    public function get_total_customers_by_email(string $email): int
    {
        $query = $this->db->query('SELECT COUNT(*) AS `total` FROM `' . DB_PREFIX . "customer` WHERE LCASE(`email`) = '" . $this->db->escape(oc_strtolower($email)) . "'");
        return (int) $query->row['total'];
    }
    /**
     * Delete Customer History
     *
     * Delete customer history record in the database.
     *
     * @param int $customer_id primary key of the customer record
     *
     *
     * @example
     *
     * $this->load->model('account/customer');
     *
     * $this->model_account_customer->deleteHistories($customer_id);
     */
    public function delete_histories(int $customer_id): void
    {
        $this->db->query('DELETE FROM `' . DB_PREFIX . "customer_history` WHERE `customer_id` = '" . $customer_id . "'");
    }
    /**
     * Delete Ips
     *
     * Delete customer ip records in the database.
     *
     * @param int $customer_id primary key of the customer record
     *
     *
     * @example
     *
     * $this->load->model('account/customer');
     *
     * $this->model_account_customer->deleteIps($customer_id);
     */
    public function delete_ips(int $customer_id): void
    {
        $this->db->query('DELETE FROM `' . DB_PREFIX . "customer_ip` WHERE `customer_id` = '" . $customer_id . "'");
    }
    /**
     * Get Ips
     *
     * Get the record of the customer ip records in the database.
     *
     * @param int $customer_id primary key of the customer record
     *
     * @return array<int, array<string, mixed>> ip records that have customer ID
     *
     * @example
     *
     * $this->load->model('account/customer');
     *
     * $results = $this->model_account_customer->getIps($customer_id);
     */
    public function get_ips(int $customer_id): array
    {
        $query = $this->db->query('SELECT * FROM `' . DB_PREFIX . "customer_ip` WHERE `customer_id` = '" . $customer_id . "'");
        return $query->rows;
    }
    /**
     * Get Total Ips
     *
     * Get the total number of total customer ip records in the database.
     *
     * @param int $customer_id primary key of the customer record
     *
     * @return int total number of ip records that have customer ID
     *
     * @example
     *
     * $this->load->model('account/customer');
     *
     * $ip_total = $this->model_account_customer->getTotalIps($customer_id);
     */
    public function get_total_ips(int $customer_id): int
    {
        $query = $this->db->query('SELECT COUNT(*) AS `total` FROM `' . DB_PREFIX . "customer_ip` WHERE `customer_id` = '" . $customer_id . "'");
        return (int) $query->row['total'];
    }
    /**
     * Add Login
     *
     * Create a new login record in the database.
     *
     * @param int    $customer_id primary key of the customer record
     *
     *
     * @example
     *
     * $this->load->model('account/customer');
     *
     * $this->model_account_customer->addLogin($customer_id, $ip, $country);
     */
    public function add_login(int $customer_id, string $ip, string $country = ''): void
    {
        $this->db->query('INSERT INTO `' . DB_PREFIX . "customer_ip` SET `customer_id` = '" . $customer_id . "', `store_id` = '" . (int) $this->config->get('config_store_id') . "', `ip` = '" . $this->db->escape($ip) . "', `country` = '" . $this->db->escape($country) . "', `date_added` = NOW()");
    }
    /**
     * Add Login Attempt
     *
     *
     *
     * @example
     *
     * $this->load->model('account/customer');
     *
     * $this->model_account_customer->addLoginAttempt($email);
     */
    public function add_login_attempt(string $email): void
    {
        $query = $this->db->query('SELECT * FROM `' . DB_PREFIX . "customer_login` WHERE LCASE(`email`) = '" . $this->db->escape(oc_strtolower($email)) . "' AND `ip` = '" . $this->db->escape(oc_get_ip()) . "'");
        if (!$query->num_rows) {
            $this->db->query('INSERT INTO `' . DB_PREFIX . "customer_login` SET `email` = '" . $this->db->escape(oc_strtolower($email)) . "', `ip` = '" . $this->db->escape(oc_get_ip()) . "', `total` = '1', `date_added` = '" . $this->db->escape(date('Y-m-d H:i:s')) . "', `date_modified` = '" . $this->db->escape(date('Y-m-d H:i:s')) . "'");
        } else {
            $this->db->query('UPDATE `' . DB_PREFIX . "customer_login` SET `total` = (`total` + 1), `date_modified` = '" . $this->db->escape(date('Y-m-d H:i:s')) . "' WHERE `customer_login_id` = '" . (int) $query->row['customer_login_id'] . "'");
        }
    }
    /**
     * Delete Customer Login Attempts
     *
     *
     *
     * @example
     *
     * $this->load->model('account/customer');
     *
     * $this->model_account_customer->deleteLoginAttempts($email);
     */
    public function delete_login_attempts(string $email): void
    {
        $this->db->query('DELETE FROM `' . DB_PREFIX . "customer_login` WHERE LCASE(`email`) = '" . $this->db->escape(oc_strtolower($email)) . "'");
    }
    /**
     * Get Login Attempts
     *
     *
     * @return array<string, mixed>
     * @example
     *
     * $this->load->model('account/customer');
     *
     * $results = $this->model_account_customer->getLoginAttempts($email);
     */
    public function get_login_attempts(string $email): array
    {
        $query = $this->db->query('SELECT * FROM `' . DB_PREFIX . "customer_login` WHERE LCASE(`email`) = '" . $this->db->escape(oc_strtolower($email)) . "'");
        return $query->row;
    }
    /**
     * Add Authorize
     *
     * Create a new customer authorize record in the database.
     *
     * @param int                  $customer_id primary key of the customer record
     * @param array<string, mixed> $data        array of data
     *
     *
     * @example
     *
     * $authorize_data = [
     *     'customer_id' => 1,
     *     'token'       => '',
     *     'ip'          => '',
     *     'user_agent'  => ''
     * ];
     *
     * $this->load->model('account/customer');
     *
     * $this->model_account_customer->addAuthorize($customer_id, $authorize_data);
     */
    public function add_authorize(int $customer_id, array $data): void
    {
        $this->db->query('INSERT INTO `' . DB_PREFIX . "customer_authorize` SET `customer_id` = '" . $customer_id . "', `token` = '" . $this->db->escape($data['token']) . "', `ip` = '" . $this->db->escape($data['ip']) . "', `user_agent` = '" . $this->db->escape($data['user_agent']) . "', `date_added` = NOW(), `date_expire` = NOW()");
    }
    /**
     * Edit Authorize Status
     *
     * Edit customer authorize status record in the database.
     *
     * @param int  $customer_authorize_id primary key of the customer authorize record
     *
     *
     * @example
     *
     * $this->load->model('account/customer');
     *
     * $this->model_account_customer->editAuthorizeStatus($customer_authorize_id, $status);
     */
    public function edit_authorize_status(int $customer_authorize_id, bool $status): void
    {
        $this->db->query('UPDATE `' . DB_PREFIX . "customer_authorize` SET `status` = '" . $status . "' WHERE `customer_authorize_id` = '" . $customer_authorize_id . "'");
    }
    /**
     * Edit Authorize Total
     *
     * @param int $customer_authorize_id primary key of the customer authorize record
     *
     *
     * @example
     *
     * $this->load->model('account/customer');
     *
     * $this->model_account_customer->editAuthorizeTotal($customer_authorize_id, $total);
     */
    public function edit_authorize_total(int $customer_authorize_id, int $total): void
    {
        $this->db->query('UPDATE `' . DB_PREFIX . "customer_authorize` SET `total` = '" . $total . "' WHERE `customer_authorize_id` = '" . $customer_authorize_id . "'");
    }
    /**
     * Delete Customer Authorizes
     *
     * Delete customer authorize records in the database.
     *
     * @param int $customer_id           primary key of the customer record
     * @param int $customer_authorize_id primary key of the customer authorize record
     *
     *
     * @example
     *
     * $this->load->model('account/customer');
     *
     * $this->model_account_customer->deleteAuthorizes($customer_id, $customer_authorize_id);
     */
    public function delete_authorizes(int $customer_id, int $customer_authorize_id = 0): void
    {
        $sql = 'DELETE FROM `' . DB_PREFIX . "customer_authorize` WHERE `customer_id` = '" . $customer_id . "'";
        if ($customer_authorize_id) {
            $sql .= " AND `customer_authorize_id` = '" . (int) $customer_authorize_id . "'";
        }
        $this->db->query($sql);
    }
    /**
     * Delete Customer Authorizes
     *
     * Delete customer authorize records in the database.
     *
     * @param int    $customer_id primary key of the customer record
     *
     *
     * @example
     *
     * $this->load->model('account/customer');
     *
     * $this->model_account_customer->deleteAuthorizeByToken($customer_id);
     */
    public function delete_authorize_by_token(int $customer_id, string $token): void
    {
        $this->db->query('DELETE FROM `' . DB_PREFIX . "customer_authorize` WHERE `customer_id` = '" . $customer_id . "' AND `token` = '" . $this->db->escape($token) . "'");
    }
    /**
     * Get Authorize By Token
     *
     * Get the record of the customer authorize by token record in the database.
     *
     * @param int    $customer_id primary key of the customer record
     *
     * @return array<string, mixed> authorize token record that has the customer ID, token
     * @example
     *
     * $this->load->model('account/customer');
     *
     * $login_info = $this->model_account_customer->getAuthorizeByToken($customer_id, $token);
     */
    public function get_authorize_by_token(int $customer_id, string $token): array
    {
        $query = $this->db->query('SELECT *, (SELECT SUM(`total`) FROM `' . DB_PREFIX . "customer_authorize` WHERE `customer_id` = '" . $customer_id . "') AS `attempts` FROM `" . DB_PREFIX . "customer_authorize` WHERE `customer_id` = '" . $customer_id . "' AND `token` = '" . $this->db->escape($token) . "'");
        return $query->row;
    }
    /**
     * Reset Customer Authorizes
     *
     * @param int $customer_id primary key of the customer record
     *
     *
     * @example
     *
     * $this->load->model('account/customer');
     *
     * $this->model_account_customer->resetAuthorizes($customer_id);
     */
    public function reset_authorizes(int $customer_id): void
    {
        $this->db->query('UPDATE `' . DB_PREFIX . "customer_authorize` SET `total` = '0' WHERE `customer_id` = '" . $customer_id . "'");
    }
    /**
     * Add Token
     *
     * Create a new customer token record in the database.
     *
     * @param int    $customer_id primary key of the customer record
     *
     * @example
     *
     * $this->load->model('account/customer');
     *
     * $authorize_total = $this->model_account_customer->addToken($customer_id, $code);
     */
    public function add_token(int $customer_id, string $type, string $code): void
    {
        $this->db->query('DELETE FROM `' . DB_PREFIX . "customer_token` WHERE `customer_id` = '" . $customer_id . "' AND `type` = '" . $this->db->escape($type) . "'");
        $this->db->query('INSERT INTO `' . DB_PREFIX . "customer_token` SET `customer_id` = '" . $customer_id . "', `code` = '" . $this->db->escape($code) . "', `type` = '" . $this->db->escape($type) . "', `date_added` = NOW()");
    }
    /**
     * Delete Token By Code
     *
     *
     *
     * @example
     *
     * $this->load->model('account/customer');
     *
     * $this->model_account_customer->deleteTokenByCode($code);
     */
    public function delete_token_by_code(string $code): void
    {
        $this->db->query('DELETE FROM `' . DB_PREFIX . "customer_token` WHERE `code` = '" . $this->db->escape($code) . "'");
    }
    /**
     * Get Token By Code
     *
     *
     * @return array<string, mixed> token record that has customer ID, code
     * @example
     *
     * $this->load->model('account/customer');
     *
     * $token_info = $this->model_account_customer->getTokenByCode($code);
     */
    public function get_token_by_code(string $code): array
    {
        $this->db->query('DELETE FROM `' . DB_PREFIX . 'customer_token` WHERE DATE_ADD(`date_added`, INTERVAL 10 MINUTE) < NOW()');
        $query = $this->db->query('SELECT * FROM `' . DB_PREFIX . 'customer_token` `ct` LEFT JOIN `' . DB_PREFIX . "customer` `c` ON (`ct`.`customer_id` = `c`.`customer_id`) WHERE `ct`.`code` = '" . $this->db->escape($code) . "'");
        return $query->row;
    }
}