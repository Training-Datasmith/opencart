<?php

declare (strict_types=1);
namespace Opencart\Admin\Controller\Startup;

/**
 * Class Extension
 *
 * @package Opencart\Admin\Controller\Startup
 */
class Extension extends \Opencart\System\Engine\Controller
{
    /**
     * Index
     */
    public function index(): void
    {
        // Add extension paths from the DB
        $this->load->model('setting/extension');
        $results = $this->model_setting_extension->get_installs();
        foreach ($results as $result) {
            $extension = str_replace(['_', '/'], ['', '\\'], ucwords($result['code'], '_/'));
            // Register controllers, models and system extension folders
            $this->autoloader->register('Opencart\Admin\Controller\Extension\\' . $extension, DIR_EXTENSION . $result['code'] . '/admin/controller/');
            $this->autoloader->register('Opencart\Admin\Model\Extension\\' . $extension, DIR_EXTENSION . $result['code'] . '/admin/model/');
            $this->autoloader->register('Opencart\System\Library\Extension\\' . $extension, DIR_EXTENSION . $result['code'] . '/system/library/');
            // Template directory
            $this->template->add_path('extension/' . $result['code'], DIR_EXTENSION . $result['code'] . '/admin/view/template/');
            // Language directory
            $this->language->add_path('extension/' . $result['code'], DIR_EXTENSION . $result['code'] . '/admin/language/');
            // Config directory
            $this->config->add_path('extension/' . $result['code'], DIR_EXTENSION . $result['code'] . '/system/config/');
        }
    }
}