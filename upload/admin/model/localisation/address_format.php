<?php

declare (strict_types=1);
namespace Opencart\Admin\Model\Localisation;

/**
 * Class Address Format
 *
 * Can be loaded using $this->load->model('localisation/address_format');
 *
 * @package Opencart\Admin\Model\Localisation
 */
class Address_Format extends \Opencart\System\Engine\Model
{
    /**
     * Add Address Format
     *
     * Create a new address format record in the database.
     *
     * @param array<string, mixed> $data array of data
     *
     *
     * @example
     *
     * $address_format_data = [
     *     'name'           => 'Address Format Name',
     *     'address_format' => ''
     * ];
     *
     * $this->load->model('localisation/address_format');
     *
     * $address_format_id = $this->model_localisation_address_format->addAddressFormat($address_format_data);
     */
    public function add_address_format(array $data): int
    {
        $this->db->query('INSERT INTO `' . DB_PREFIX . "address_format` SET `name` = '" . $this->db->escape((string) $data['name']) . "', `address_format` = '" . $this->db->escape((string) $data['address_format']) . "'");
        return $this->db->get_last_id();
    }
    /**
     * Edit Address Format
     *
     * Edit address format record in the database.
     *
     * @param int                  $address_format_id primary key of the address format record
     * @param array<string, mixed> $data              array of data
     *
     *
     * @example
     *
     * $address_format_data = [
     *     'name'           => 'Address Format Name',
     *     'address_format' => ''
     * ];
     *
     * $this->load->model('localisation/address_format');
     *
     * $this->model_localisation_address_format->editAddressFormat($address_format_id, $address_format_data);
     */
    public function edit_address_format(int $address_format_id, array $data): void
    {
        $this->db->query('UPDATE `' . DB_PREFIX . "address_format` SET `name` = '" . $this->db->escape((string) $data['name']) . "', `address_format` = '" . $this->db->escape((string) $data['address_format']) . "' WHERE `address_format_id` = '" . $address_format_id . "'");
    }
    /**
     * Delete Address Format
     *
     * Delete address format record in the database.
     *
     * @param int $address_format_id primary key of the address format record
     *
     *
     * @example
     *
     * $this->load->model('localisation/address_format');
     *
     * $this->model_localisation_address_format->deleteAddressFormat($address_format_id);
     */
    public function delete_address_format(int $address_format_id): void
    {
        $this->db->query('DELETE FROM `' . DB_PREFIX . "address_format` WHERE `address_format_id` = '" . $address_format_id . "'");
    }
    /**
     * Get Address Format
     *
     * Get the record of the address format record in the database.
     *
     * @param int $address_format_id primary key of the address format record
     *
     * @return array<string, mixed> address format record that has address format ID
     *
     * @example
     *
     * $this->load->model('localisation/address_format');
     *
     * $address_format_info = $this->model_localisation_address_format->getAddressFormat($address_format_id);
     */
    public function get_address_format(int $address_format_id): array
    {
        $query = $this->db->query('SELECT DISTINCT * FROM `' . DB_PREFIX . "address_format` WHERE `address_format_id` = '" . $address_format_id . "'");
        return $query->row;
    }
    /**
     * Get Address Formats
     *
     * Get the record of the address format records in the database.
     *
     * @param array<string, mixed> $data array of filters
     *
     * @return array<int, array<string, mixed>> address format records
     *
     * @example
     *
     * $filter_data = [
     *     'start' => 0,
     *     'limit' => 10
     * ];
     *
     * $this->load->model('localisation/address_format');
     *
     * $results = $this->model_localisation_address_format->getAddressFormats($filter_data);
     */
    public function get_address_formats(array $data = []): array
    {
        $sql = 'SELECT * FROM `' . DB_PREFIX . 'address_format`';
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
     * Get Total Address Formats
     *
     * Get the total number of address format records in the database.
     *
     * @param array<string, mixed> $data array of filters
     *
     * @return int total number of address format records
     *
     * @example
     *
     * $filter_data = [
     *     'start' => 0,
     *     'limit' => 10
     * ];
     *
     * $this->load->model('localisation/address_format');
     *
     * $address_format_total = $this->model_localisation_address_format->getTotalAddressFormats($filter_data);
     */
    public function get_total_address_formats(array $data = []): int
    {
        $query = $this->db->query('SELECT COUNT(*) AS `total` FROM `' . DB_PREFIX . 'address_format`');
        return (int) $query->row['total'];
    }
}