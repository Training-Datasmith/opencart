<?php

declare (strict_types=1);
namespace Opencart\Admin\Controller\Task\Catalog;

/**
 * Class Topic
 *
 * @package Opencart\Admin\Controller\Task\Catalog
 */
class Topic extends \Opencart\System\Engine\Controller
{
    /**
     * Index
     *
     * Generate all country data.
     *
     * @param array<string, string> $args
     */
    public function add_topic(array $args = []): array
    {
        $this->load->language('task/catalog/topic');
        return ['success' => $this->language->get('text_task')];
    }
}