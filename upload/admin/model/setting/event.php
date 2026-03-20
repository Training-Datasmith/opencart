<?php

declare (strict_types=1);
namespace Opencart\Admin\Model\Setting;

/**
 * Class Event
 *
 * Can be loaded using $this->load->model('setting/event');
 *
 * @package Opencart\Admin\Model\Setting
 */
class Event extends \Opencart\System\Engine\Model
{
    /**
     * Add Event
     *
     * Create a new event record in the database.
     *
     * @param array<string, mixed> $data array of of data
     *
     * @return int returns the primary key of the new event record
     *
     * @example
     *
     * $event_data = [
     *     'code'        => 'Event Code',
     *     'description' => 'Event Description',
     *     'trigger'     => 'Event Trigger',
     *     'action'      => 'Event Action',
     *     'status'      => 0,
     *     'sort_order'  => 0
     * ];
     *
     * $this->load->model('setting/event');
     *
     * $event_id = $this->model_setting_event->addEvent($event_data);
     */
    public function add_event(array $data): int
    {
        $this->db->query('INSERT INTO `' . DB_PREFIX . "event` SET `code` = '" . $this->db->escape($data['code']) . "', `description` = '" . $this->db->escape($data['description']) . "', `trigger` = '" . $this->db->escape($data['trigger']) . "', `action` = '" . $this->db->escape($data['action']) . "', `status` = '" . (bool) $data['status'] . "', `sort_order` = '" . (int) $data['sort_order'] . "'");
        return $this->db->get_last_id();
    }
    /**
     * Delete Event
     *
     * Delete event record in the database.
     *
     * @param int $event_id primary key of the event record
     *
     *
     * @example
     *
     * $this->load->model('setting/event');
     *
     * $this->model_setting_event->deleteEvent($event_id);
     */
    public function delete_event(int $event_id): void
    {
        $this->db->query('DELETE FROM `' . DB_PREFIX . "event` WHERE `event_id` = '" . $event_id . "'");
    }
    /**
     * Delete Event By Code
     *
     *
     *
     * @example
     *
     * $this->load->model('setting/event');
     *
     * $this->model_setting_event->deleteEventByCode($code);
     */
    public function delete_event_by_code(string $code): void
    {
        $this->db->query('DELETE FROM `' . DB_PREFIX . "event` WHERE `code` = '" . $this->db->escape($code) . "'");
    }
    /**
     * Edit Status
     *
     * Edit event status record in the database.
     *
     * @param int  $event_id primary key of the event record
     *
     *
     * @example
     *
     * $this->load->model('setting/event');
     *
     * $this->model_setting_event->editStatus($event_id, $status);
     */
    public function edit_status(int $event_id, bool $status): void
    {
        $this->db->query('UPDATE `' . DB_PREFIX . "event` SET `status` = '" . $status . "' WHERE `event_id` = '" . $event_id . "'");
    }
    /**
     * Edit Status By Code
     *
     *
     *
     * @example
     *
     * $this->load->model('setting/event');
     *
     * $this->model_setting_event->editStatusByCode($code, $status);
     */
    public function edit_status_by_code(string $code, bool $status): void
    {
        $this->db->query('UPDATE `' . DB_PREFIX . "event` SET `status` = '" . $status . "' WHERE `code` = '" . $this->db->escape($code) . "'");
    }
    /**
     * Get Event
     *
     * Get the record of the event record in the database.
     *
     * @param int $event_id primary key of the event record
     *
     * @return array<string, mixed> event record that has event ID
     *
     * @example
     *
     * $this->load->model('setting/event');
     *
     * $event_info = $this->model_setting_event->getEvent($event_id);
     */
    public function get_event(int $event_id): array
    {
        $query = $this->db->query('SELECT * FROM `' . DB_PREFIX . "event` WHERE `event_id` = '" . $event_id . "'");
        return $query->row;
    }
    /**
     * Get Event By Code
     *
     *
     * @return array<string, mixed>
     * @example
     *
     * $this->load->model('setting/event');
     *
     * $event_info = $this->model_setting_event->getEventByCode($code);
     */
    public function get_event_by_code(string $code): array
    {
        $query = $this->db->query('SELECT DISTINCT * FROM `' . DB_PREFIX . "event` WHERE `code` = '" . $this->db->escape($code) . "' LIMIT 1");
        return $query->row;
    }
    /**
     * Get Events
     *
     * Get the record of the event records in the database.
     *
     * @param array<string, mixed> $data array of filters
     *
     * @return array<int, array<string, mixed>> event records
     *
     * @example
     *
     * $filter_data = [
     *     'sort'  => 'code',
     *     'order' => 'DESC',
     *     'start' => 0,
     *     'limit' => 10
     * ];
     *
     * $this->load->model('setting/event');
     *
     * $results = $this->model_setting_event->getEvents($filter_data);
     */
    public function get_events(array $data = []): array
    {
        $sql = 'SELECT * FROM `' . DB_PREFIX . 'event`';
        $implode = [];
        if (!empty($data['filter_code'])) {
            $implode[] = "LCASE(`code`) LIKE '" . $this->db->escape(oc_strtolower($data['filter_code'])) . "'";
        }
        if (isset($data['filter_status']) && $data['filter_status'] !== '') {
            $implode[] = "`status` = '" . (int) $data['filter_status'] . "'";
        }
        if ($implode) {
            $sql .= ' WHERE ' . implode(' AND ', $implode);
        }
        $sql .= ' ORDER BY `code` ASC, `sort_order` ASC';
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
     * Get Total Events
     *
     * Get the total number of total event records in the database.
     *
     * @return int total number of event records
     *
     * @example
     *
     * $this->load->model('setting/event');
     *
     * $event_total = $this->model_setting_event->getTotalEvents();
     */
    public function get_total_events(array $data = []): int
    {
        $sql = 'SELECT COUNT(*) AS `total` FROM `' . DB_PREFIX . 'event`';
        $implode = [];
        if (!empty($data['filter_code'])) {
            $implode[] = "LCASE(`code`) LIKE '" . $this->db->escape(oc_strtolower($data['filter_code'])) . "'";
        }
        if (isset($data['filter_status']) && $data['filter_status'] !== '') {
            $implode[] = "`status` = '" . (int) $data['filter_status'] . "'";
        }
        if ($implode) {
            $sql .= ' WHERE ' . implode(' AND ', $implode);
        }
        $query = $this->db->query($sql);
        return (int) $query->row['total'];
    }
}