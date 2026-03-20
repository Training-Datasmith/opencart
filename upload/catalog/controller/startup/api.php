<?php

declare (strict_types=1);
namespace Opencart\Catalog\Controller\Startup;

/**
 * Class Api
 *
 * @package Opencart\Catalog\Controller\Startup
 */
class Api extends \Opencart\System\Engine\Controller
{
    /**
     * Index
     */
    public function index(): ?\Opencart\System\Engine\Action
    {
        if (isset($this->request->get['route'])) {
            $route = (string) $this->request->get['route'];
        } else {
            $route = '';
        }
        $allowed = ['api/order', 'api/subscription'];
        // Block direct access to other methods
        if (str_starts_with($route, 'api/') && !in_array($route, $allowed)) {
            return new \Opencart\System\Engine\Action('startup/api.permission');
        }
        if (in_array($route, $allowed)) {
            $status = true;
            $required = ['route', 'call', 'username', 'store_id', 'language', 'currency', 'time', 'signature'];
            foreach ($required as $key) {
                if (!isset($this->request->get[$key])) {
                    $status = false;
                }
            }
            if ($status) {
                // Api
                $this->load->model('user/api');
                $api_info = $this->model_user_api->get_api_by_username((string) $this->request->get['username']);
                if ($api_info) {
                    // Check if IP is allowed
                    $ip_data = [];
                    $results = $this->model_user_api->get_ips($api_info['api_id']);
                    foreach ($results as $result) {
                        $ip_data[] = trim($result['ip']);
                    }
                    if (!in_array(oc_get_ip(), $ip_data)) {
                        $status = false;
                    }
                } else {
                    $status = false;
                }
                $time = $this->request->get['time'];
                $time_start = time() - 450;
                $time_end = time() + 450;
                if ($time < $time_start || $time > $time_end) {
                    $status = false;
                }
            }
            if ($status) {
                $string = $this->request->get['route'] . "\n";
                $string .= $this->request->get['call'] . "\n";
                $string .= $api_info['username'] . "\n";
                $string .= $this->request->server['HTTP_HOST'] . "\n";
                $string .= (!empty($this->request->server['PHP_SELF']) ? rtrim(dirname($this->request->server['PHP_SELF']), '/') . '/' : '/') . "\n";
                $string .= (int) $this->request->get['store_id'] . "\n";
                $string .= $this->request->get['language'] . "\n";
                $string .= $this->request->get['currency'] . "\n";
                $string .= md5(http_build_query($this->request->post)) . "\n";
                $string .= $time . "\n";
                if (rawurldecode($this->request->get['signature']) != base64_encode(hash_hmac('sha1', $string, $api_info['key'], true))) {
                    $status = false;
                }
            }
            if ($status) {
                $this->model_user_api->add_history($api_info['api_id'], $this->request->get['call'], oc_get_ip());
            } else {
                return new \Opencart\System\Engine\Action('startup/api.permission');
            }
        }
        return null;
    }
    /**
     * Permission
     */
    public function permission(): void
    {
        $this->language->load('error/permission');
        $this->response->add_header($this->request->server['SERVER_PROTOCOL'] . ' 403 Forbidden');
        $this->response->set_output(json_encode(['error' => $this->language->get('text_error')]));
    }
}