<?php

declare (strict_types=1);
namespace Opencart\Catalog\Model\Account;

/**
 * Class Activity
 *
 * Can be called using $this->load->model('account/activity');
 *
 * @package Opencart\Catalog\Model\Account
 */
class Activity extends \Opencart\System\Engine\Model
{
    /**
     * Add Activity
     *
     * Create a new activity record in the database.
     *
     * @param array<string, mixed> $data array of data
     *
     *
     * @example
     *
     * $activity_data = [
     *     'key'  => '',
     *     'data' => [],
     * ];
     *
     * $this->load->model('account/activity');
     *
     * $this->model_account_activity->addActivity($key, $activity_data);
     */
    public function add_activity(string $key, array $data): void
    {
        if (isset($data['customer_id'])) {
            $customer_id = $data['customer_id'];
        } else {
            $customer_id = 0;
        }
        $this->db->query('INSERT INTO `' . DB_PREFIX . "customer_activity` SET `customer_id` = '" . (int) $customer_id . "', `key` = '" . $this->db->escape($key) . "', `data` = '" . $this->db->escape(json_encode($data)) . "', `ip` = '" . $this->db->escape(oc_get_ip()) . "', `date_added` = NOW()");
    }
    /**
     * Delete Activities
     *
     * Delete activities records in the database.
     *
     * @param int $customer_id primary key of the customer record
     *
     *
     * @example
     *
     * $this->load->model('account/activity');
     *
     * $this->model_account_activity->deleteActivities($customer_id);
     */
    public function delete_activities(int $customer_id): void
    {
        $this->db->query('DELETE FROM `' . DB_PREFIX . "customer_activity` WHERE `customer_id` = '" . $customer_id . "'");
    }
}