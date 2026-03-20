<?php

declare (strict_types=1);
namespace Opencart\Catalog\Model\Design;

/**
 * Class Seo Regex
 *
 * Can be called using $this->load->model('design/seo_regex');
 *
 * @package Opencart\Catalog\Model\Design
 */
class Seo_Regex extends \Opencart\System\Engine\Model
{
    /**
     * Get Seo Regexes
     *
     * @param string $keyword
     *
     * @return array<string, mixed>
     *
     * @example
     *
     * $this->load->model('design/seo_regex');
     *
     * $results = $this->model_design_seo_regex->getSeoRegexes();
     */
    public function get_seo_regexes(): array
    {
        $query = $this->db->query('SELECT DISTINCT * FROM `' . DB_PREFIX . 'seo_regex`');
        return $query->rows;
    }
}