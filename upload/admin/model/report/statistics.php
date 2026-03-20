<?php

declare (strict_types=1);
namespace Opencart\Admin\Model\Report;

/**
 * Class Statistics
 *
 * Can be loaded using $this->load->model('report/statistics');
 *
 * @package Opencart\Admin\Model\Report
 */
class Statistics extends \Opencart\System\Engine\Model
{
    /**
     * Get Statistics
     *
     * Get the record of the statistic records in the database.
     *
     * @return array<int, array<string, mixed>> statistic records
     *
     * @example
     *
     * $this->load->model('report/statistics');
     *
     * $results = $this->model_report_statistics->getStatistics();
     */
    public function get_statistics(): array
    {
        $query = $this->db->query('SELECT * FROM `' . DB_PREFIX . 'statistics`');
        return $query->rows;
    }
    /**
     * Get Value
     *
     *
     *
     * @example
     *
     * $this->load->model('report/statistics');
     *
     * $value = (float)$this->model_report_statistics->getValue($code);
     */
    public function get_value(string $code): float
    {
        $query = $this->db->query('SELECT `value` FROM `' . DB_PREFIX . "statistics` WHERE `code` = '" . $this->db->escape($code) . "'");
        if ($query->num_rows) {
            return $query->row['value'];
        }
        return 0;
    }
    /**
     * Add Value
     *
     *
     *
     * @example
     *
     * $this->load->model('report/statistics');
     *
     * $this->model_report_statistics->addValue($code, $value);
     */
    public function add_value(string $code, float $value): void
    {
        $this->db->query('UPDATE `' . DB_PREFIX . "statistics` SET `value` = (`value` + '" . $value . "') WHERE `code` = '" . $this->db->escape($code) . "'");
    }
    /**
     * Remove Value
     *
     *
     *
     * @example
     *
     * $this->load->model('report/statistics');
     *
     * $this->model_report_statistics->removeValue($code, $value);
     */
    public function remove_value(string $code, float $value): void
    {
        $this->db->query('UPDATE `' . DB_PREFIX . "statistics` SET `value` = (`value` - '" . $value . "') WHERE `code` = '" . $this->db->escape($code) . "'");
    }
    /**
     * Edit Value
     *
     *
     *
     * @example
     *
     * $this->load->model('report/statistics');
     *
     * $this->model_report_statistics->editValue($code, $value);
     */
    public function edit_value(string $code, float $value): void
    {
        $this->db->query('UPDATE `' . DB_PREFIX . "statistics` SET `value` = '" . $value . "' WHERE `code` = '" . $this->db->escape($code) . "'");
    }
}