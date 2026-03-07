<?php

declare(strict_types=1);

namespace Opencart\Install\Controller\Upgrade;

/**
 * Class Upgrade19
 *
 * @package Opencart\Install\Controller\Upgrade
 */
class Upgrade19 extends \Opencart\System\Engine\Controller
{
    /**
     * Index
     */
    public function index(): void
    {
        $this->load->language('upgrade/upgrade');

        $json = [];
        $json['success'] = $this->language->get('text_success');
        $url = '';
        if (isset($this->request->get['admin'])) {
            $url .= '&admin=' . $this->request->get['admin'];
        }
        $json['redirect'] = $this->url->link('install/step_4', $url, true);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}
