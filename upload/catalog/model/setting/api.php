<?php

declare (strict_types=1);
namespace Opencart\Catalog\Model\Setting;

/**
 * Class Api
 *
 * Can be called using $this->load->model('setting/api');
 *
 * @package Opencart\Catalog\Model\Setting
 */
class Api extends \Opencart\System\Engine\Model
{
    /**
     * Login
     *
     *
     * @return array<string, mixed>
     *
     * @example
     *
     * $this->load->model('setting/api');
     *
     * $api_info = $this->model_setting_api->login($username, $key);
     */
    public function login(string $username, string $key): array
    {
        $query = $this->db->query('SELECT * FROM `' . DB_PREFIX . 'api` `a` LEFT JOIN `' . DB_PREFIX . "api_ip` `ai` ON (`a`.`api_id` = `ai`.`api_id`) WHERE `a`.`username` = '" . $this->db->escape($username) . "' AND `a`.`key` = '" . $this->db->escape($key) . "'");
        return $query->row;
    }
    /**
     * Get Api By Token
     *
     *
     * @return array<string, mixed>
     * @example
     *
     * $this->load->model('setting/api');
     *
     * $api_info = $this->model_setting_api->getApiByToken($token);
     */
    public function get_api_by_token(string $token): array
    {
        $query = $this->db->query('SELECT DISTINCT * FROM `' . DB_PREFIX . 'api` `a` LEFT JOIN `' . DB_PREFIX . 'api_session` `as` ON (`a`.`api_id` = `as`.`api_id`) LEFT JOIN `' . DB_PREFIX . "api_ip` `ai` ON (`a`.`api_id` = `ai`.`api_id`) WHERE `a`.`status` = '1' AND `as`.`session_id` = '" . $this->db->escape($token) . "' AND `ai`.`ip` = '" . $this->db->escape(oc_get_ip()) . "'");
        return $query->row;
    }
    /**
     * Get Sessions
     *
     * Get the record of the api session records in the database.
     *
     * @param int $api_id primary key of the Api record
     *
     * @return array<int, array<string, mixed>> session records that have api ID
     *
     * @example
     *
     * $this->load->model('setting/api');
     *
     * $api_sessions = $this->model_setting_api->getSessions($api_id);
     */
    public function get_sessions(int $api_id): array
    {
        $query = $this->db->query('SELECT * FROM `' . DB_PREFIX . "api_session` WHERE TIMESTAMPADD(HOUR, 1, `date_modified`) < NOW() AND `api_id` = '" . $api_id . "'");
        return $query->rows;
    }
    /**
     * Delete API Sessions
     *
     * Delete api session records in the database.
     *
     * @param int $api_id primary key of the Api record
     *
     * @return array<int, array<string, mixed>>
     *
     * @example
     *
     * $this->load->model('setting/api');
     *
     * $this->model_setting_api->deleteSessions($api_id);
     */
    public function delete_sessions(int $api_id): array
    {
        $query = $this->db->query('SELECT * FROM `' . DB_PREFIX . "api_session` WHERE TIMESTAMPADD(HOUR, 1, `date_modified`) < NOW() AND `api_id` = '" . $api_id . "'");
        return $query->rows;
    }
    /**
     * Update Session
     *
     * @param string $api_session_id primary key of the Api Session record
     *
     *
     * @example
     *
     * $this->load->model('setting/api');
     *
     * $this->model_setting_api->updateSession($api_session_id);
     */
    public function update_session(string $api_session_id): void
    {
        // keep the session alive
        $this->db->query('UPDATE `' . DB_PREFIX . "api_session` SET `date_modified` = NOW() WHERE `api_session_id` = '" . (int) $api_session_id . "'");
    }
    /**
     * Clean API Sessions
     *
     *
     * @example
     *
     * $this->load->model('setting/api');
     *
     * $this->model_setting_api->cleanSessions();
     */
    public function clean_sessions(): void
    {
        $this->db->query('DELETE FROM `' . DB_PREFIX . 'api_session` WHERE TIMESTAMPADD(HOUR, 1, `date_modified`) < NOW()');
    }
}