<?php

declare (strict_types=1);
namespace Opencart\Admin\Controller\Startup;

/**
 * Class Startup
 *
 * @package Opencart\Admin\Controller\Startup
 */
class Startup extends \Opencart\System\Engine\Controller
{
    /**
     * Index
     */
    public function index(): void
    {
        // Load startup actions
        $this->load->model('setting/startup');
        $results = $this->model_setting_startup->get_startups();
        foreach ($results as $result) {
            if (str_starts_with($result['action'], 'admin/') && $result['status']) {
                $this->load->controller(substr($result['action'], 6));
            }
        }
    }
}