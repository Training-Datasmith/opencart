<?php

declare (strict_types=1);
namespace Opencart\Catalog\Controller\Common;

/**
 * Class Home
 *
 * Can be called from $this->load->controller('common/home');
 *
 * @package Opencart\Catalog\Controller\Common
 */
class Home extends \Opencart\System\Engine\Controller
{
    /**
     * Index
     */
    public function index(): void
    {
        $description = $this->config->get('config_description');
        $language_id = $this->config->get('config_language_id');
        if (isset($description[$language_id])) {
            $this->document->set_title($description[$language_id]['meta_title']);
            $this->document->set_description($description[$language_id]['meta_description']);
            $this->document->set_keywords($description[$language_id]['meta_keyword']);
        }
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');
        $this->response->set_output($this->load->view('common/home', $data));
    }
}