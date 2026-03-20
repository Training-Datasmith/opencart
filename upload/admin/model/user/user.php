<?php

declare (strict_types=1);
namespace Opencart\Admin\Model\User;

/**
 * Class User
 *
 * Can be loaded using $this->load->model('user/user');
 *
 * @package Opencart\Admin\Model\User
 */
class User extends \Opencart\System\Engine\Model
{
    /**
     * Add User
     *
     * Create a new user record in the database.
     *
     * @param array<string, mixed> $data array of data
     *
     *
     * @example
     *
     * $user_data = [
     *     'username'      => 'Username',
     *     'user_group_id' => 1,
     *     'password'      => '',
     *     'firstname'     => 'John',
     *     'lastname'      => 'Doe',
     *     'email'         => 'demo@opencart.com',
     *     'image'         => 'user_image',
     *     'status'        => 0
     * ];
     *
     * $this->load->model('user/user');
     *
     * $user_id = $this->model_user_user->addUser($user_data);
     */
    public function add_user(array $data): int
    {
        $this->db->query('INSERT INTO `' . DB_PREFIX . "user` SET `username` = '" . $this->db->escape((string) $data['username']) . "', `user_group_id` = '" . (int) $data['user_group_id'] . "', `password` = '" . $this->db->escape(password_hash(html_entity_decode($data['password'], ENT_QUOTES, 'UTF-8'), PASSWORD_DEFAULT)) . "', `firstname` = '" . $this->db->escape((string) $data['firstname']) . "', `lastname` = '" . $this->db->escape((string) $data['lastname']) . "', `email` = '" . $this->db->escape((string) $data['email']) . "', `image` = '" . $this->db->escape((string) $data['image']) . "', `status` = '" . (bool) ($data['status'] ?? 0) . "', `date_added` = NOW()");
        return $this->db->get_last_id();
    }
    /**
     * Edit User
     *
     * Edit user status record in the database.
     *
     * @param int                  $user_id primary key of the user record
     * @param array<string, mixed> $data    array of data
     *
     *
     * @example
     *
     * $user_data = [
     *     'username'      => 'Username',
     *     'user_group_id' => 1,
     *     'password'      => '',
     *     'firstname'     => 'John',
     *     'lastname'      => 'Doe',
     *     'email'         => 'demo@opencart.com',
     *     'image'         => 'user_image',
     *     'status'        => 1
     * ];
     *
     * $this->load->model('user/user');
     *
     * $this->model_user_user->editUser($user_id, $user_data);
     */
    public function edit_user(int $user_id, array $data): void
    {
        $this->db->query('UPDATE `' . DB_PREFIX . "user` SET `username` = '" . $this->db->escape((string) $data['username']) . "', `user_group_id` = '" . (int) $data['user_group_id'] . "', `firstname` = '" . $this->db->escape((string) $data['firstname']) . "', `lastname` = '" . $this->db->escape((string) $data['lastname']) . "', `email` = '" . $this->db->escape((string) $data['email']) . "', `image` = '" . $this->db->escape((string) $data['image']) . "', `status` = '" . (bool) ($data['status'] ?? 0) . "' WHERE `user_id` = '" . $user_id . "'");
        if ($data['password']) {
            $this->db->query('UPDATE `' . DB_PREFIX . "user` SET `password` = '" . $this->db->escape(password_hash(html_entity_decode($data['password'], ENT_QUOTES, 'UTF-8'), PASSWORD_DEFAULT)) . "' WHERE `user_id` = '" . $user_id . "'");
        }
    }
    /**
     * Edit Password
     *
     * Edit user password record in the database.
     *
     * @param int    $user_id  primary key of the user record
     * @param string $password
     *
     *
     * @example
     *
     * $this->load->model('user/user');
     *
     * $this->model_user_user->editPassword($user_id, $password);
     */
    public function edit_password(int $user_id, $password): void
    {
        $this->db->query('UPDATE `' . DB_PREFIX . "user` SET `password` = '" . $this->db->escape(password_hash(html_entity_decode($password, ENT_QUOTES, 'UTF-8'), PASSWORD_DEFAULT)) . "', `code` = '' WHERE `user_id` = '" . $user_id . "'");
    }
    /**
     * Delete User
     *
     * Delete user record in the database.
     *
     * @param int $user_id primary key of the user record
     *
     *
     * @example
     *
     * $this->load->model('user/user');
     *
     * $this->model_user_user->deleteUser($user_id);
     */
    public function delete_user(int $user_id): void
    {
        $this->db->query('DELETE FROM `' . DB_PREFIX . "user` WHERE `user_id` = '" . $user_id . "'");
        $this->delete_authorizes($user_id);
        $this->delete_logins($user_id);
    }
    /**
     * Get User
     *
     * Get the record of the user record in the database.
     *
     * @param int $user_id primary key of the user record
     *
     * @return array<string, mixed> user record that has user ID
     *
     * @example
     *
     * $this->load->model('user/user');
     *
     * $user_info = $this->model_user_user->getUser($user_id);
     */
    public function get_user(int $user_id): array
    {
        $query = $this->db->query('SELECT *, (SELECT `ug`.`name` FROM `' . DB_PREFIX . 'user_group` `ug` WHERE `ug`.`user_group_id` = `u`.`user_group_id`) AS `user_group` FROM `' . DB_PREFIX . "user` `u` WHERE `u`.`user_id` = '" . $user_id . "'");
        return $query->row;
    }
    /**
     * Get User By Username
     *
     *
     * @return array<string, mixed>
     * @example
     *
     * $this->load->model('user/user');
     *
     * $user_info = $this->model_user_user->getUserByUsername($username);
     */
    public function get_user_by_username(string $username): array
    {
        $query = $this->db->query('SELECT * FROM `' . DB_PREFIX . "user` WHERE `username` = '" . $this->db->escape($username) . "'");
        return $query->row;
    }
    /**
     * Get User By Email
     *
     *
     * @return array<string, mixed>
     * @example
     *
     * $this->load->model('user/user');
     *
     * $user_info = $this->model_user_user->getUserByEmail($email);
     */
    public function get_user_by_email(string $email): array
    {
        $query = $this->db->query('SELECT DISTINCT * FROM `' . DB_PREFIX . "user` WHERE LCASE(`email`) = '" . $this->db->escape(oc_strtolower($email)) . "'");
        return $query->row;
    }
    /**
     * Get User By Code
     *
     *
     * @return array<string, mixed>
     * @example
     *
     * $this->load->model('user/user');
     *
     * $user_info = $this->model_user_user->getUserByCode($code);
     */
    public function get_user_by_code(string $code): array
    {
        $query = $this->db->query('SELECT * FROM `' . DB_PREFIX . "user` WHERE `code` = '" . $this->db->escape($code) . "' AND `code` != ''");
        return $query->row;
    }
    /**
     * Get Users
     *
     * Get the record of the user records in the database.
     *
     * @param array<string, mixed> $data array of filters
     *
     * @return array<int, array<string, mixed>> user records
     *
     * @example
     *
     * $filter_data = [
     *     'filter_username'      => 'Username',
     *     'filter_name'          => 'User Name',
     *     'filter_email'         => 'demo@opencart.com',
     *     'filter_user_group_id' => 1,
     *     'filter_status'        => 1,
     *     'filter_ip'            => '',
     *     'sort'                 => 'username',
     *     'order'                => 'DESC',
     *     'start'                => 0,
     *     'limit'                => 10
     * ];
     *
     * $this->load->model('user/user');
     *
     * $results = $this->model_user_user->getUsers($filter_data);
     */
    public function get_users(array $data = []): array
    {
        $sql = "SELECT *, CONCAT(`u`.`firstname`, ' ', `u`.`lastname`) AS `name`, (SELECT `ug`.`name` FROM `" . DB_PREFIX . 'user_group` `ug` WHERE `ug`.`user_group_id` = `u`.`user_group_id`) AS `user_group` FROM `' . DB_PREFIX . 'user` `u`';
        $implode = [];
        if (!empty($data['filter_username'])) {
            $implode[] = "LCASE(`u`.`username`) LIKE '" . $this->db->escape(oc_strtolower($data['filter_username']) . '%') . "'";
        }
        if (!empty($data['filter_name'])) {
            $implode[] = "LCASE(CONCAT(`u`.`firstname`, ' ', `u`.`lastname`)) LIKE '" . $this->db->escape('%' . oc_strtolower($data['filter_name']) . '%') . "'";
        }
        if (!empty($data['filter_email'])) {
            $implode[] = "LCASE(`u`.`email`) LIKE '" . $this->db->escape(oc_strtolower($data['filter_email']) . '%') . "'";
        }
        if (!empty($data['filter_user_group_id'])) {
            $implode[] = "`u`.`user_group_id` = '" . (int) $data['filter_user_group_id'] . "'";
        }
        if (!empty($data['filter_ip'])) {
            $implode[] = '`u`.`user_id` IN (SELECT `user_id` FROM `' . DB_PREFIX . "user_login` WHERE `ip` LIKE '" . $this->db->escape('%' . oc_strtolower($data['filter_ip']) . '%') . "')";
        }
        if (isset($data['filter_status']) && $data['filter_status'] !== '') {
            $implode[] = "`u`.`status` = '" . (int) $data['filter_status'] . "'";
        }
        if ($implode) {
            $sql .= ' WHERE ' . implode(' AND ', $implode);
        }
        $sort_data = ['username' => 'username', 'name' => 'name', 'customer' => 'customer', 'email' => 'u.email', 'user_group' => 'user_group', 'status' => 'status', 'ip' => 'ip', 'date_added' => 'u.date_added'];
        if (isset($data['sort']) && array_key_exists($data['sort'], $sort_data)) {
            $sql .= ' ORDER BY ' . $sort_data[$data['sort']];
        } else {
            $sql .= ' ORDER BY `username`';
        }
        if (isset($data['order']) && $data['order'] == 'DESC') {
            $sql .= ' DESC';
        } else {
            $sql .= ' ASC';
        }
        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }
            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }
            $sql .= ' LIMIT ' . (int) $data['start'] . ',' . (int) $data['limit'];
        }
        $query = $this->db->query($sql);
        return $query->rows;
    }
    /**
     * Get Total Users
     *
     * Get the total number of total user records in the database.
     *
     * @param array<string, mixed> $data array of filters
     *
     * @return int total number of user records
     *
     * @example
     *
     * $filter_data = [
     *     'filter_username'      => 'Username',
     *     'filter_name'          => 'User Name',
     *     'filter_email'         => 'demo@opencart.com',
     *     'filter_user_group_id' => 1,
     *     'filter_status'        => 1,
     *     'filter_ip'            => '',
     *     'sort'                 => 'username',
     *     'order'                => 'DESC',
     *     'start'                => 0,
     *     'limit'                => 10
     * ];
     *
     * $this->load->model('user/user');
     *
     * $user_total = $this->model_user_user->getTotalUsers($filter_data);
     */
    public function get_total_users(array $data = []): int
    {
        $sql = 'SELECT COUNT(*) AS `total` FROM `' . DB_PREFIX . 'user` `u` ';
        $implode = [];
        if (!empty($data['filter_username'])) {
            $implode[] = "LCASE(`u`.`username`) LIKE '" . $this->db->escape(oc_strtolower($data['filter_username']) . '%') . "'";
        }
        if (!empty($data['filter_name'])) {
            $implode[] = "LCASE(CONCAT(`u`.`firstname`, ' ', `u`.`lastname`)) LIKE '" . $this->db->escape('%' . oc_strtolower($data['filter_name']) . '%') . "'";
        }
        if (!empty($data['filter_email'])) {
            $implode[] = "LCASE(`u`.`email`) LIKE '" . $this->db->escape(oc_strtolower($data['filter_email']) . '%') . "'";
        }
        if (!empty($data['filter_user_group_id'])) {
            $implode[] = "`u`.`user_group_id` = '" . (int) $data['filter_user_group_id'] . "'";
        }
        if (!empty($data['filter_ip'])) {
            $implode[] = '`u`.`user_id` IN (SELECT `user_id` FROM `' . DB_PREFIX . "user_login` WHERE `ip` LIKE '" . $this->db->escape('%' . oc_strtolower($data['filter_ip']) . '%') . "')";
        }
        if (isset($data['filter_status']) && $data['filter_status'] !== '') {
            $implode[] = "`u`.`status` = '" . (int) $data['filter_status'] . "'";
        }
        if ($implode) {
            $sql .= ' WHERE ' . implode(' AND ', $implode);
        }
        $query = $this->db->query($sql);
        return (int) $query->row['total'];
    }
    /**
     * Get Total Users By Group ID
     *
     * Get the total number of total users by group records in the database.
     *
     * @param int $user_group_id primary key of the user group record
     *
     * @return int total number of user records that have user group ID
     *
     * @example
     *
     * $this->load->model('user/user');
     *
     * $user_total = $this->model_user_user->getTotalUsersByGroupId($user_group_id);
     */
    public function get_total_users_by_group_id(int $user_group_id): int
    {
        $query = $this->db->query('SELECT COUNT(*) AS `total` FROM `' . DB_PREFIX . "user` WHERE `user_group_id` = '" . $user_group_id . "'");
        return (int) $query->row['total'];
    }
    /**
     * Get Total Users By Email
     *
     *
     *
     * @example
     *
     * $this->load->model('user/user');
     *
     * $user_total = $this->model_user_user->getTotalusersByEmail($email);
     */
    public function get_total_users_by_email(string $email): int
    {
        $query = $this->db->query('SELECT COUNT(*) AS `total` FROM `' . DB_PREFIX . "user` WHERE LCASE(`email`) = '" . $this->db->escape(oc_strtolower($email)) . "'");
        return (int) $query->row['total'];
    }
    /**
     * Add Login
     *
     * Create a new user login record in the database.
     *
     * @param int                  $user_id primary key of the user record
     * @param array<string, mixed> $data    array of data
     *
     *
     * @example
     *
     * $user_login_data = [
     *     'ip'         => '',
     *     'user_agent' => ''
     * ];
     *
     * $this->load->model('user/user');
     *
     * $this->model_user_user->addLogin($user_id, $user_login_data);
     */
    public function add_login(int $user_id, array $data): void
    {
        $this->db->query('INSERT INTO `' . DB_PREFIX . "user_login` SET `user_id` = '" . $user_id . "', `ip` = '" . $this->db->escape($data['ip']) . "', `user_agent` = '" . $this->db->escape($data['user_agent']) . "', `date_added` = NOW()");
    }
    /**
     * Delete User Logins
     *
     * Delete user login records in the database.
     *
     * @param int $user_id primary key of the user record
     *
     *
     * @example
     *
     * $this->load->model('user/user');
     *
     * $this->model_user_user->deleteLogins($user_id);
     */
    public function delete_logins(int $user_id): void
    {
        $this->db->query('DELETE FROM `' . DB_PREFIX . "user_login` WHERE `user_id` = '" . $user_id . "'");
    }
    /**
     * Get Logins
     *
     * Get the record of the user login records in the database.
     *
     * @param int $user_id primary key of the user record
     *
     * @return array<int, array<string, mixed>> login records that have user ID
     *
     * @example
     *
     * $this->load->model('user/user');
     *
     * $results = $this->model_user_user->getLogins($user_id, $start, $limit);
     */
    public function get_logins(int $user_id, int $start = 0, int $limit = 10): array
    {
        if ($start < 0) {
            $start = 0;
        }
        if ($limit < 1) {
            $limit = 10;
        }
        $query = $this->db->query('SELECT * FROM `' . DB_PREFIX . "user_login` WHERE `user_id` = '" . $user_id . "' ORDER BY `date_added` DESC LIMIT " . $start . ',' . $limit);
        if ($query->num_rows) {
            return $query->rows;
        }
        return [];
    }
    /**
     * Get Total Logins
     *
     * Get the total number of total user login records in the database.
     *
     * @param int $user_id primary key of the user record
     *
     * @return int total number of login records that have user ID
     *
     * @example
     *
     * $this->load->model('user/user');
     *
     * $login_total = $this->model_user_user->getTotalLogins($user_id);
     */
    public function get_total_logins(int $user_id): int
    {
        $query = $this->db->query('SELECT COUNT(*) AS `total` FROM `' . DB_PREFIX . "user_login` WHERE `user_id` = '" . $user_id . "'");
        if ($query->num_rows) {
            return (int) $query->row['total'];
        }
        return 0;
    }
    /**
     * Add Authorize
     *
     * Create a new user authorize record in the database.
     *
     * @param int                  $user_id primary key of the user record
     * @param array<string, mixed> $data    array of data
     *
     *
     * @example
     *
     * $user_authorize_data = [
     *     'token'      => '',
     *     'ip'         => '',
     *     'user_agent' => ''
     * ];
     *
     * $this->load->model('user/user');
     *
     * $this->model_user_user->addAuthorize($user_id, $user_authorize_data);
     */
    public function add_authorize(int $user_id, array $data): void
    {
        $this->db->query('INSERT INTO `' . DB_PREFIX . "user_authorize` SET `user_id` = '" . $user_id . "', `token` = '" . $this->db->escape($data['token']) . "', `ip` = '" . $this->db->escape($data['ip']) . "', `user_agent` = '" . $this->db->escape($data['user_agent']) . "', `date_added` = NOW(), `date_expire` = NOW()");
    }
    /**
     * Edit Authorize Status
     *
     * Edit user authorize status record in the database.
     *
     * @param int  $user_authorize_id primary key of the user authorize record
     *
     *
     * @example
     *
     * $this->load->model('user/user');
     *
     * $this->model_user_user->editAuthorizeStatus($user_authorize_id, $status);
     */
    public function edit_authorize_status(int $user_authorize_id, bool $status): void
    {
        $this->db->query('UPDATE `' . DB_PREFIX . "user_authorize` SET `status` = '" . $status . "' WHERE `user_authorize_id` = '" . $user_authorize_id . "'");
    }
    /**
     * Edit Authorize Total
     *
     * Edit user authorize total record in the database.
     *
     * @param int $user_authorize_id primary key of the user authorize record
     *
     *
     * @example
     *
     * $this->load->model('user/user');
     *
     * $this->model_user_user->editAuthorizeTotal($user_authorize_id, $total);
     */
    public function edit_authorize_total(int $user_authorize_id, int $total): void
    {
        $this->db->query('UPDATE `' . DB_PREFIX . "user_authorize` SET `total` = '" . $total . "' WHERE `user_authorize_id` = '" . $user_authorize_id . "'");
    }
    /**
     * Edit Authorize Total By User ID
     *
     * Edit user authorize total by user record in the database.
     *
     * @param int $user_id primary key of the user record
     *
     *
     * @example
     *
     * $this->load->model('user/user');
     *
     * $this->model_user_user->editAuthorizeTotalByUserId($user_id, $total);
     */
    public function edit_authorize_total_by_user_id(int $user_id, int $total): void
    {
        $this->db->query('UPDATE `' . DB_PREFIX . "user_authorize` SET `total` = '" . $total . "' WHERE `user_id` = '" . $user_id . "'");
    }
    /**
     * Delete User Authorizes
     *
     * Delete user authorize records in the database.
     *
     * @param int $user_id           primary key of the user record
     * @param int $user_authorize_id primary key of the user authorize record
     *
     *
     * @example
     *
     * $this->load->model('user/user');
     *
     * $this->model_user_user->deleteAuthorizes($user_id, $user_authorize_id);
     */
    public function delete_authorizes(int $user_id, int $user_authorize_id = 0): void
    {
        $sql = 'DELETE FROM `' . DB_PREFIX . "user_authorize` WHERE `user_id` = '" . $user_id . "'";
        if ($user_authorize_id) {
            $sql .= " AND `user_authorize_id` = '" . (int) $user_authorize_id . "'";
        }
        $this->db->query($sql);
    }
    /**
     * Get Authorize
     *
     * Get the record of the user authorize record in the database.
     *
     * @param int $user_authorize_id primary key of the user authorize record
     *
     * @return array<string, mixed> authorize record that has user authorize ID
     *
     * @example
     *
     * $this->load->model('user/user');
     *
     * $authorize_info = $this->model_user_user->getAuthorize($user_authorize_id);
     */
    public function get_authorize(int $user_authorize_id): array
    {
        $query = $this->db->query('SELECT * FROM `' . DB_PREFIX . "user_authorize` WHERE `user_authorize_id` = '" . $user_authorize_id . "'");
        return $query->row;
    }
    /**
     * Get Authorize By Token
     *
     * @param int    $user_id primary key of the user record
     *
     * @return array<string, mixed> authorize record that has user ID, token
     * @example
     *
     * $this->load->model('user/user');
     *
     * $authorize_info = $this->model_user_user->getAuthorizeByToken($user_id, $token);
     */
    public function get_authorize_by_token(int $user_id, string $token): array
    {
        $query = $this->db->query('SELECT *, (SELECT SUM(`total`) FROM `' . DB_PREFIX . "user_authorize` WHERE `user_id` = '" . $user_id . "') AS `attempts` FROM `" . DB_PREFIX . "user_authorize` WHERE `user_id` = '" . $user_id . "' AND `token` = '" . $this->db->escape($token) . "'");
        return $query->row;
    }
    /**
     * Get Authorizes
     *
     * Get the record of the user authorize records in the database.
     *
     * @param  int  $user_id  primary key of the user record
     *
     * @return array<int, array<string, mixed>> authorize records
     *
     * @example
     *
     * $this->load->model('user/user');
     *
     * $results = $this->model_user_user->getAuthorizes($user_id, $start, $limit);
     */
    public function get_authorizes(int $user_id, int $start = 0, int $limit = 10): array
    {
        if ($start < 0) {
            $start = 0;
        }
        if ($limit < 1) {
            $limit = 10;
        }
        $query = $this->db->query('SELECT * FROM `' . DB_PREFIX . "user_authorize` WHERE `user_id` = '" . $user_id . "' LIMIT " . $start . ',' . $limit);
        if ($query->num_rows) {
            return $query->rows;
        }
        return [];
    }
    /**
     * Get Total Authorizes
     *
     * Get the total number of total user authorize records in the database.
     *
     * @param int $user_id primary key of the user record
     *
     * @return int total number of authorize records that have user ID
     *
     * @example
     *
     * $this->load->model('user/user');
     *
     * $authorize_total = $this->model_user_user->getTotalAuthorizes($user_id);
     */
    public function get_total_authorizes(int $user_id): int
    {
        $query = $this->db->query('SELECT COUNT(*) AS `total` FROM `' . DB_PREFIX . "user_authorize` WHERE `user_id` = '" . $user_id . "'");
        if ($query->num_rows) {
            return (int) $query->row['total'];
        }
        return 0;
    }
    /**
     * Reset User Authorizes
     *
     * Reset user authorize record in the database.
     *
     * @param int $user_id primary key of the user record
     *
     *
     * @example
     *
     * $this->load->model('user/user');
     *
     * $this->model_user_user->resetAuthorizes($user_id);
     */
    public function reset_authorizes(int $user_id): void
    {
        $this->db->query('UPDATE `' . DB_PREFIX . "user_authorize` SET `total` = '0' WHERE `user_id` = '" . $user_id . "'");
    }
    /**
     * Add Token
     *
     * Create a new user token record in the database.
     *
     * @param  int  $user_id  primary key of the user record
     *
     *
     * @example
     *
     * $this->load->model('user/user');
     *
     * $authorize_total = $this->model_user_user->addToken($user_id, $code, $type);
     */
    public function add_token(int $user_id, string $type, string $code): void
    {
        $this->db->query('DELETE FROM `' . DB_PREFIX . "user_token` WHERE `user_id` = '" . $user_id . "' AND `type` = '" . $this->db->escape($type) . "'");
        $this->db->query('INSERT INTO `' . DB_PREFIX . "user_token` SET `user_id` = '" . $user_id . "', `code` = '" . $this->db->escape($code) . "', `type` = '" . $this->db->escape($type) . "', `date_added` = NOW()");
    }
    /**
     * Get Token By Code
     *
     *
     * @return array<string, mixed> token record that has user ID, code
     * @example
     *
     * $this->load->model('user/user');
     *
     * $token_info = $this->model_user_user->getTokenByCode($user_id, $code);
     */
    public function get_token_by_code(string $code): array
    {
        $this->db->query('DELETE FROM `' . DB_PREFIX . 'user_token` WHERE DATE_ADD(`date_added`, INTERVAL 10 MINUTE) < NOW()');
        $query = $this->db->query('SELECT * FROM `' . DB_PREFIX . 'user_token` `ut` LEFT JOIN `' . DB_PREFIX . "user` `u` ON (`ut`.`user_id` = `u`.`user_id`) WHERE `ut`.`code` = '" . $this->db->escape($code) . "'");
        return $query->row;
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
     * $this->model_account_customer->deleteToken($customer_id);
     */
    public function delete_token_by_code(string $code): void
    {
        $this->db->query('DELETE FROM `' . DB_PREFIX . "user_token` WHERE `code` = '" . $this->db->escape($code) . "'");
    }
}