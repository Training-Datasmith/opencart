<?php

declare(strict_types=1);

namespace Opencart\Catalog\Model\Design;

/**
 * Class Theme
 *
 * Can be called using $this->load->model('design/theme');
 *
 * @package Opencart\Catalog\Model\Design
 */
class Theme extends \Opencart\System\Engine\Model
{
    /**
     * Get Theme
     *
     * Get the record of the theme record in the database.
     *
     *
     * @return array<string, mixed>
     * @example
     *
     * $this->load->model('design/theme');
     *
     * $theme_info = $this->model_design_theme->getTheme($route);
     */
    public function getTheme(string $route): array
    {
        $query = $this->db->query('SELECT * FROM `' . DB_PREFIX . "theme` WHERE `store_id` = '" . (int)$this->config->get('config_store_id') . "' AND `route` = '" . $this->db->escape($route) . "' AND `status` = '1'");

        return $query->row;
    }
}
