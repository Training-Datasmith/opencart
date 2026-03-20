<?php

declare (strict_types=1);
namespace Opencart\Catalog\Model\Localisation;

/**
 * Class Address Format
 *
 * Can be called using $this->load->model('localisation/address_format');
 *
 * @package Opencart\Admin\Model\Localisation
 */
class Address_Format extends \Opencart\System\Engine\Model
{
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
}