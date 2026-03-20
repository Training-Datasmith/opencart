<?php

declare (strict_types=1);
namespace Opencart\Catalog\Controller\Startup;

/**
 * Class Maintenance
 *
 * @package Opencart\Catalog\Controller\Startup
 */
class Maintenance extends \Opencart\System\Engine\Controller
{
    /**
     * Index
     */
    public function index(): ?\Opencart\System\Engine\Action
    {
        if ($this->config->get('config_maintenance')) {
            // Route
            if (isset($this->request->get['route'])) {
                $route = $this->request->get['route'];
            } else {
                $route = $this->config->get('action_default');
            }
            $ignore = ['common/language/language', 'common/currency/currency'];
            // Show site if logged in as admin
            $user = new \Opencart\System\Library\Cart\User($this->registry);
            if (!str_starts_with($route, 'api') && !in_array($route, $ignore) && !$user->is_logged()) {
                return new \Opencart\System\Engine\Action('common/maintenance');
            }
        }
        return null;
    }
}