<?php

declare (strict_types=1);
namespace Opencart\Admin\Model\Tool;

/**
 * Class Notification
 *
 * Can be loaded using $this->load->model('tool/notification');
 *
 * @package Opencart\Admin\Model\Tool
 */
class Notification extends \Opencart\System\Engine\Model
{
    /**
     * Add Notification
     *
     * Create a new notification record in the database.
     *
     * @param array<string, mixed> $data array of data
     *
     *
     * @example
     *
     * $notification_data = [
     *     'title'  => 'Notification Title',
     *     'text'   => 'Notification Text',
     *     'status' => 0
     * ];
     *
     * $this->load->model('tool/notification');
     *
     * $notification_id = $this->model_tool_notification->addNotification($notification_data);
     */
    public function add_notification(array $data): int
    {
        $this->db->query('INSERT INTO `' . DB_PREFIX . "notification` SET `title` = '" . $this->db->escape((string) $data['title']) . "', `text` = '" . $this->db->escape((string) $data['text']) . "', `status` = '" . (bool) $data['status'] . "', `date_added` = NOW()");
        return $this->db->get_last_id();
    }
    /**
     * Edit Status
     *
     * Edit notification status record in the database.
     *
     * @param int  $notification_id primary key of the notification record
     *
     *
     * @example
     *
     * $this->load->model('tool/notification');
     *
     * $this->model_tool_notification->editStatus($notification_id, $status);
     */
    public function edit_status(int $notification_id, bool $status): void
    {
        $this->db->query('UPDATE `' . DB_PREFIX . "notification` SET `status` = '" . $status . "' WHERE `notification_id` = '" . $notification_id . "'");
    }
    /**
     * Delete Notification
     *
     * Delete notification record in the database.
     *
     * @param int $notification_id primary key of the notification record
     *
     *
     * @example
     *
     * $this->load->model('tool/notification');
     *
     * $this->model_tool_notification->deleteNotification($notification_id);
     */
    public function delete_notification(int $notification_id): void
    {
        $this->db->query('DELETE FROM `' . DB_PREFIX . "notification` WHERE `notification_id` = '" . $notification_id . "'");
    }
    /**
     * Get Notification
     *
     * Get the record of the notification record in the database.
     *
     * @param int $notification_id primary key of the notification record
     *
     * @return array<string, mixed> notification record that has notification ID
     *
     * @example
     *
     * $this->load->model('tool/notification');
     *
     * $notification_info = $this->model_tool_notification->getNotification($notification_id);
     */
    public function get_notification(int $notification_id): array
    {
        $query = $this->db->query('SELECT DISTINCT * FROM `' . DB_PREFIX . "notification` WHERE `notification_id` = '" . $notification_id . "'");
        return $query->row;
    }
    /**
     * Get Notifications
     *
     * Get the record of the notification records in the database.
     *
     * @param array<string, mixed> $data array of filters
     *
     * @return array<int, array<string, mixed>> notification records
     *
     * @example
     *
     * $filter_data = [
     *     'start'  => 0,
     *     'limit'  => 5,
     *     'status' => 1
     * ];
     *
     * $this->load->model('tool/notification');
     *
     * $results = $this->model_tool_notification->getNotifications($filter_data);
     */
    public function get_notifications(array $data = []): array
    {
        $sql = 'SELECT * FROM `' . DB_PREFIX . 'notification`';
        if (isset($data['filter_status']) && $data['filter_status'] !== '') {
            $sql .= " WHERE `status` = '" . (bool) $data['filter_status'] . "'";
        }
        $sql .= ' ORDER BY `date_added` DESC';
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
     * Get Total Notifications
     *
     * Get the total number of total notification records in the database.
     *
     * @param array<string, mixed> $data array of filters
     *
     * @return int total number of notification records
     *
     * @example
     *
     * $filter_data = [
     *     'start'  => 0,
     *     'limit'  => 5,
     *     'status' => 1
     * ];
     *
     * $this->load->model('tool/notification');
     *
     * $notification_total = $this->model_tool_notification->getTotalNotifications();
     */
    public function get_total_notifications(array $data = []): int
    {
        $sql = 'SELECT COUNT(*) AS `total` FROM `' . DB_PREFIX . 'notification`';
        if (isset($data['filter_status']) && $data['filter_status'] !== '') {
            $sql .= " WHERE `status` = '" . (bool) $data['filter_status'] . "'";
        }
        $query = $this->db->query($sql);
        return (int) $query->row['total'];
    }
}