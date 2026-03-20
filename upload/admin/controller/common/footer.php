<?php

declare (strict_types=1);
namespace Opencart\Admin\Controller\Common;

/**
 * Class Footer
 *
 * Can be loaded using $this->load->controller('common/footer');
 *
 * @package Opencart\Admin\Controller\Common
 */
class Footer extends \Opencart\System\Engine\Controller
{
    /**
     * Index
     */
    public function index(): string
    {
        $this->load->language('common/footer');
        if ($this->user->is_logged() && isset($this->request->get['user_token']) && $this->request->get['user_token'] == $this->session->data['user_token']) {
            $data['text_version'] = sprintf($this->language->get('text_version'), VERSION);
        } else {
            $data['text_version'] = '';
        }
        // Hard coding css so they can be replaced via the event's system.
        $data['scripts'] = $this->document->get_scripts();
        return $this->load->view('common/footer', $data);
    }
}