<?php

declare(strict_types=1);

namespace Opencart\Admin\Model\Extension\Opencart\Fraud;

/**
 * Class Ip
 *
 * Can be called from $this->load->model('extension/opencart/fraud/ip');
 *
 * @package Opencart\Admin\Controller\Extension\Opencart\Fraud
 */
class Ip extends \Opencart\System\Engine\Model
{
    /**
     * Install
     *
     *
     * @example
     *
     * $this->model_extension_opencart_fraud_ip->install();
     */
    public function install(): void
    {
        $this->db->query('CREATE TABLE IF NOT EXISTS `' . DB_PREFIX . 'fraud_ip` (
		  `ip` varchar(40) NOT NULL,
		  `date_added` datetime NOT NULL,
		  PRIMARY KEY (`ip`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
    }

    /**
     * Uninstall
     *
     *
     * @example
     *
     * $this->model_extension_opencart_fraud_ip->uninstall();
     */
    public function uninstall(): void
    {
        $this->db->query('DROP TABLE IF EXISTS `' . DB_PREFIX . 'fraud_ip`');
    }

    /**
     * Add Ip
     *
     *
     *
     * @example
     *
     * $this->model_extension_opencart_fraud_ip->addIp($ip);
     */
    public function addIp(string $ip): void
    {
        $this->db->query('INSERT INTO `' . DB_PREFIX . "fraud_ip` SET `ip` = '" . $this->db->escape($ip) . "', `date_added` = NOW()");
    }

    /**
     * Remove Ip
     *
     *
     *
     * @example
     *
     * $this->model_extension_opencart_fraud_ip->removeIp($ip);
     */
    public function removeIp(string $ip): void
    {
        $this->db->query('DELETE FROM `' . DB_PREFIX . "fraud_ip` WHERE `ip` = '" . $this->db->escape($ip) . "'");
    }

    /**
     * Get Ips
     *
     *
     * @return array<int, array<string, mixed>>
     *
     * @example
     *
     * $results = $this->model_extension_opencart_fraud_ip->getIps();
     */
    public function getIps(int $start = 0, int $limit = 10): array
    {
        if ($start < 0) {
            $start = 0;
        }

        if ($limit < 1) {
            $limit = 10;
        }

        $query = $this->db->query('SELECT * FROM `' . DB_PREFIX . 'fraud_ip` ORDER BY `ip` ASC LIMIT ' . $start . ',' . $limit);

        return $query->rows;
    }

    /**
     * Get Total Ips
     *
     *
     * @example
     *
     * $ip_total = $this->model_extension_opencart_fraud_ip->getTotalIps();
     */
    public function getTotalIps(): int
    {
        $query = $this->db->query('SELECT COUNT(*) AS `total` FROM `' . DB_PREFIX . 'fraud_ip`');

        return (int)$query->row['total'];
    }

    /**
     * Get Total Ips By Ip
     *
     *
     *
     * @example
     *
     * $ip_total = $this->model_extension_opencart_fraud_ip->getTotalIpsByIp($ip);
     */
    public function getTotalIpsByIp(string $ip): int
    {
        $query = $this->db->query('SELECT COUNT(*) AS `total` FROM `' . DB_PREFIX . "fraud_ip` WHERE `ip` = '" . $this->db->escape($ip) . "'");

        return (int)$query->row['total'];
    }
}
