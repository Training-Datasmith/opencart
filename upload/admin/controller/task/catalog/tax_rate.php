<?php

declare (strict_types=1);
namespace Opencart\Admin\Controller\Task\Catalog;

/**
 * Class Tax Rate
 *
 * @package Opencart\Admin\Controller\Task\Catalog
 */
class Tax_Rate extends \Opencart\System\Engine\Controller
{
    /**
     * Info
     *
     * Generate tax rate information.
     *
     * @param array<string, string> $args
     */
    public function index(array $args = []): array
    {
        $this->load->language('task/catalog/tax_rate');
        if (!array_key_exists('geo_zone_id', $args)) {
            return ['error' => $this->language->get('error_required')];
        }
        $this->load->model('localisation/geo_zone');
        $geo_zone_info = $this->model_localisation_geo_zone->get_geo_zone($args['geo_zone_id']);
        if (!$geo_zone_info) {
            return ['error' => $this->language->get('error_geo_zone')];
        }
        $tax_rate_data = [];
        $this->load->model('localisation/tax_rate');
        $tax_rates = $this->model_localisation_tax_rate->get_tax_rates_by_geo_zone_id($geo_zone_info['geo_zone_id']);
        foreach ($tax_rates as $tax_rate) {
            $customer_groups = $this->model_localisation_tax_rate->get_customer_groups($tax_rate['tax_rate_id']);
            foreach ($customer_groups as $customer_group_id) {
                $tax_rate_data[] = $tax_rate + ['customer_group_id' => $customer_group_id];
            }
        }
        $directory = DIR_CATALOG . 'view/data/localisation/';
        $filename = 'tax_rate-' . $geo_zone_info['geo_zone_id'] . '.yaml';
        if (!oc_directory_create($directory, 0777)) {
            return ['error' => sprintf($this->language->get('error_directory'), $directory)];
        }
        if (!file_put_contents($directory . $filename, oc_yaml_encode($tax_rate_data))) {
            return ['error' => sprintf($this->language->get('error_file'), $directory . $filename)];
        }
        return ['success' => sprintf($this->language->get('text_info'), $geo_zone_info['name'])];
    }
    /**
     * Clear
     *
     * Delete generated JSON tax rate files.
     *
     * @param array<string, string> $args
     */
    public function clear(array $args = []): array
    {
        $this->load->language('task/catalog/tax_rate');
        $files = oc_directory_read(DIR_CATALOG . 'view/data/localisation/', false, '/tax_rate\-.+\.json$/');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        return ['success' => $this->language->get('text_clear')];
    }
}