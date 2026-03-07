<?php

declare(strict_types=1);

namespace Opencart\Install\Controller\Startup;

/**
 * Class Upgrade
 *
 * @package Opencart\Install\Controller\Startup
 */
class Upgrade extends \Opencart\System\Engine\Controller
{
    /**
     * Index
     */
    public function index(): void
    {
        $upgrade = false;

        if (is_file(DIR_OPENCART . 'config.php') && filesize(DIR_OPENCART . 'config.php') > 0) {
            $upgrade = true;
        }

        if (isset($this->request->get['route']) && ((str_starts_with($this->request->get['route'], 'upgrade/')) || (str_starts_with($this->request->get['route'], 'install/step_4')))) {
            $upgrade = false;
        }

        if ($upgrade) {
            $this->response->redirect($this->url->link('upgrade/upgrade'));
        }
    }
}
