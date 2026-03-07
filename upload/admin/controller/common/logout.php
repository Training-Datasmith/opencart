<?php

declare(strict_types=1);

namespace Opencart\Admin\Controller\Common;

/**
 * Class Logout
 *
 * Can be loaded using $this->load->controller('common/logout');
 *
 * @package Opencart\Admin\Controller\Common
 */
class Logout extends \Opencart\System\Engine\Controller
{
    /**
     * Index
     */
    public function index(): void
    {
        $this->user->logout();

        unset($this->session->data['user_token']);

        $this->response->redirect($this->url->link('common/login', '', true));
    }
}
