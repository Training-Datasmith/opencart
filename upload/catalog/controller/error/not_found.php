<?php

declare (strict_types=1);
namespace Opencart\Catalog\Controller\Error;

/**
 * Class Not Found
 *
 * @package Opencart\Catalog\Controller\Error
 */
class Not_Found extends \Opencart\System\Engine\Controller
{
    /**
     * Index
     */
    public function index(): void
    {
        $this->load->language('error/not_found');
        $this->document->set_title($this->language->get('heading_title'));
        $data['breadcrumbs'] = [];
        $data['breadcrumbs'][] = ['text' => $this->language->get('text_home'), 'href' => $this->url->link('common/home', 'language=' . $this->config->get('config_language'))];
        if (isset($this->request->get['route'])) {
            $url_data = $this->request->get;
            $route = $url_data['route'];
            unset($url_data['route']);
            $url = '';
            if ($url_data) {
                $url .= '&' . urldecode(http_build_query($url_data, '', '&'));
            }
            $data['breadcrumbs'][] = ['text' => $this->language->get('heading_title'), 'href' => $this->url->link($route, $url)];
        }
        $data['continue'] = $this->url->link('common/home', 'language=' . $this->config->get('config_language'));
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');
        $this->response->add_header($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');
        $this->response->set_output($this->load->view('error/not_found', $data));
    }
}