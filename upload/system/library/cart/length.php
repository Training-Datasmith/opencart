<?php

declare(strict_types=1);

namespace Opencart\System\Library\Cart;

/**
 * Class Length
 *
 * @package Opencart\System\Library\Cart
 */
class Length
{
    private object $db;
    private object $config;
    /**
     * @var array<int, array<string, mixed>>
     */
    private array $lengths = [];

    /**
     * Constructor
     */
    public function __construct(\Opencart\System\Engine\Registry $registry)
    {
        $this->db = $registry->get('db');
        $this->config = $registry->get('config');

        $length_class_query = $this->db->query('SELECT * FROM `' . DB_PREFIX . 'length_class` `mc` LEFT JOIN `' . DB_PREFIX . "length_class_description` `mcd` ON (`mc`.`length_class_id` = `mcd`.`length_class_id`) WHERE `mcd`.`language_id` = '" . (int)$this->config->get('config_language_id') . "'");

        foreach ($length_class_query->rows as $result) {
            $this->lengths[$result['length_class_id']] = [
                'length_class_id' => $result['length_class_id'],
                'title'           => $result['title'],
                'unit'            => $result['unit'],
                'value'           => $result['value'],
            ];
        }
    }

    /**
     * Convert
     *
     *
     *
     * @example
     *
     * $length = $this->length->convert($value, $from, $to);
     */
    public function convert(float $value, int $from, int $to): float
    {
        if ($from == $to) {
            return $value;
        }

        if (isset($this->lengths[$from])) {
            $from = $this->lengths[$from]['value'];
        } else {
            $from = 1;
        }

        if (isset($this->lengths[$to])) {
            $to = $this->lengths[$to]['value'];
        } else {
            $to = 1;
        }

        return $value * ($to / $from);
    }

    /**
     * Format
     *
     * @param int    $length_class_id primary key of the length class record
     *
     *
     * @example
     *
     * $length = $this->length->format($value, $length_class_id, $decimal_point, $thousand_point);
     */
    public function format(float $value, int $length_class_id, string $decimal_point = '.', string $thousand_point = ','): string
    {
        if (isset($this->lengths[$length_class_id])) {
            return number_format($value, 2, $decimal_point, $thousand_point) . $this->lengths[$length_class_id]['unit'];
        }
        return number_format($value, 2, $decimal_point, $thousand_point);
    }

    /**
     * Get Unit
     *
     * @param int $length_class_id primary key of the length class record
     *
     *
     * @example
     *
     * $unit = $this->length->getUnit($length_class_id);
     */
    public function getUnit(int $length_class_id): string
    {
        if (isset($this->lengths[$length_class_id])) {
            return $this->lengths[$length_class_id]['unit'];
        }
        return '';
    }
}
