<?php

declare (strict_types=1);
namespace Opencart\Admin\Model\Localisation;

/**
 * Class Language
 *
 * Can be loaded using $this->load->model('localisation/language');
 *
 * @package Opencart\Admin\Model\Localisation
 */
class Language extends \Opencart\System\Engine\Model
{
    /**
     * Add Language
     *
     * Create a new language record in the database.
     *
     * @param array<string, mixed> $data array of data
     *
     * @return int returns the primary key of the new language record
     *
     * @example
     *
     * $language_data = [
     *     'name'       => 'Language Name',
     *     'code'       => 'Language Code',
     *     'locale'     => 'Language Locale',
     *     'extension'  => '',
     *     'sort_order' => 0,
     *     'status'     => 0
     * ];
     *
     * $this->load->model('localisation/language');
     *
     * $language_id = $this->model_localisation_language->addLanguage($language_data);
     */
    public function add_language(array $data): int
    {
        $this->db->query('INSERT INTO `' . DB_PREFIX . "language` SET `name` = '" . $this->db->escape((string) $data['name']) . "', `code` = '" . $this->db->escape((string) $data['code']) . "', `locale` = '" . $this->db->escape((string) $data['locale']) . "', `extension` = '" . $this->db->escape((string) $data['extension']) . "', `sort_order` = '" . (int) $data['sort_order'] . "', `status` = '" . (bool) ($data['status'] ?? 0) . "'");
        $this->cache->delete('language');
        $language_id = $this->db->get_last_id();
        // Attribute
        $this->load->model('catalog/attribute');
        $results = $this->model_catalog_attribute->get_descriptions_by_language_id($this->config->get('config_language_id'));
        foreach ($results as $attribute) {
            $this->model_catalog_attribute->add_description((int) $attribute['attribute_id'], $language_id, $attribute);
        }
        // Attribute Group
        $this->load->model('catalog/attribute_group');
        $results = $this->model_catalog_attribute_group->get_descriptions_by_language_id($this->config->get('config_language_id'));
        foreach ($results as $attribute_group) {
            $this->model_catalog_attribute_group->add_description((int) $attribute_group['attribute_group_id'], $language_id, $attribute_group);
        }
        // Banner
        $this->load->model('design/banner');
        $results = $this->model_design_banner->get_images_by_language_id($this->config->get('config_language_id'));
        foreach ($results as $banner_image) {
            $this->model_design_banner->add_image($banner_image['banner_id'], $language_id, $banner_image);
        }
        // Category
        $this->load->model('catalog/category');
        $results = $this->model_catalog_category->get_descriptions_by_language_id($this->config->get('config_language_id'));
        foreach ($results as $category) {
            $this->model_catalog_category->add_description((int) $category['category_id'], $language_id, $category);
        }
        // Country
        $this->load->model('localisation/country');
        $results = $this->model_localisation_country->get_descriptions_by_language_id($this->config->get('config_language_id'));
        foreach ($results as $country) {
            $this->model_localisation_country->add_description((int) $country['country_id'], $language_id, $country);
        }
        // Customer Group
        $this->load->model('customer/customer_group');
        $results = $this->model_customer_customer_group->get_descriptions_by_language_id($this->config->get('config_language_id'));
        foreach ($results as $customer_group) {
            $this->model_customer_customer_group->add_description((int) $customer_group['customer_group_id'], $language_id, $customer_group);
        }
        // Custom Field
        $this->load->model('customer/custom_field');
        $results = $this->model_customer_custom_field->get_descriptions_by_language_id($this->config->get('config_language_id'));
        foreach ($results as $custom_field) {
            $this->model_customer_custom_field->add_description((int) $custom_field['custom_field_id'], $language_id, $custom_field);
        }
        // Custom Field Value
        $results = $this->model_customer_custom_field->get_value_descriptions_by_language_id($this->config->get('config_language_id'));
        foreach ($results as $custom_field_value) {
            $this->model_customer_custom_field->add_value_description((int) $custom_field_value['custom_field_value_id'], (int) $custom_field_value['custom_field_id'], $language_id, $custom_field_value);
        }
        // Download
        $this->load->model('catalog/download');
        $results = $this->model_catalog_download->get_descriptions_by_language_id($this->config->get('config_language_id'));
        foreach ($results as $download) {
            $this->model_catalog_download->add_description((int) $download['download_id'], $language_id, $download);
        }
        // Filter
        $this->load->model('catalog/filter');
        $results = $this->model_catalog_filter->get_descriptions_by_language_id($this->config->get('config_language_id'));
        foreach ($results as $filter) {
            $this->model_catalog_filter->add_description((int) $filter['filter_id'], $language_id, $filter);
        }
        // Filter Group
        $this->load->model('catalog/filter_group');
        $results = $this->model_catalog_filter_group->get_descriptions_by_language_id($this->config->get('config_language_id'));
        foreach ($results as $filter_group) {
            $this->model_catalog_filter_group->add_description((int) $filter_group['filter_group_id'], $language_id, $filter_group);
        }
        // Information
        $this->load->model('catalog/information');
        $results = $this->model_catalog_information->get_descriptions_by_language_id($this->config->get('config_language_id'));
        foreach ($results as $information) {
            $this->model_catalog_information->add_description((int) $information['information_id'], $language_id, $information);
        }
        // Length
        $this->load->model('localisation/length_class');
        $results = $this->model_localisation_length_class->get_descriptions_by_language_id($this->config->get('config_language_id'));
        foreach ($results as $length) {
            $this->model_localisation_length_class->add_description((int) $length['length_class_id'], $language_id, $length);
        }
        // Manufacturer
        $this->load->model('catalog/manufacturer');
        $results = $this->model_catalog_manufacturer->get_descriptions_by_language_id($this->config->get('config_language_id'));
        foreach ($results as $manufacturer) {
            $this->model_catalog_manufacturer->add_description((int) $manufacturer['manufacturer_id'], $language_id, $manufacturer);
        }
        // Option
        $this->load->model('catalog/option');
        $results = $this->model_catalog_option->get_descriptions_by_language_id($this->config->get('config_language_id'));
        foreach ($results as $option) {
            $this->model_catalog_option->add_description((int) $option['option_id'], $language_id, $option);
        }
        // Option Value
        $results = $this->model_catalog_option->get_value_descriptions_by_language_id($this->config->get('config_language_id'));
        foreach ($results as $option_value) {
            $this->model_catalog_option->add_value_description((int) $option_value['option_value_id'], (int) $option_value['option_id'], $language_id, $option_value);
        }
        // Order Status
        $this->load->model('localisation/order_status');
        $results = $this->model_localisation_order_status->get_descriptions_by_language_id($this->config->get('config_language_id'));
        foreach ($results as $order_status) {
            $this->model_localisation_order_status->add_description((int) $order_status['order_status_id'], $language_id, $order_status);
        }
        // Product
        $this->load->model('catalog/product');
        $results = $this->model_catalog_product->get_descriptions_by_language_id($this->config->get('config_language_id'));
        foreach ($results as $product) {
            $this->model_catalog_product->add_description((int) $product['product_id'], $language_id, $product);
        }
        // Product Attribute
        $results = $this->model_catalog_product->get_attributes_by_language_id($this->config->get('config_language_id'));
        foreach ($results as $product_attribute) {
            $this->model_catalog_product->add_attribute((int) $product_attribute['product_id'], (int) $product_attribute['attribute_id'], $language_id, $product_attribute);
        }
        // Return Action
        $this->load->model('localisation/return_action');
        $results = $this->model_localisation_return_action->get_descriptions_by_language_id($this->config->get('config_language_id'));
        foreach ($results as $return_action) {
            $this->model_localisation_return_action->add_description((int) $return_action['return_action_id'], $language_id, $return_action);
        }
        // Return Reason
        $this->load->model('localisation/return_reason');
        $results = $this->model_localisation_return_reason->get_descriptions_by_language_id($this->config->get('config_language_id'));
        foreach ($results as $return_reason) {
            $this->model_localisation_return_reason->add_description((int) $return_reason['return_reason_id'], $language_id, $return_reason);
        }
        // Return Status
        $this->load->model('localisation/return_status');
        $results = $this->model_localisation_return_status->get_descriptions_by_language_id($this->config->get('config_language_id'));
        foreach ($results as $return_status) {
            $this->model_localisation_return_status->add_description((int) $return_status['return_status_id'], $language_id, $return_status);
        }
        // Stock Status
        $this->load->model('localisation/stock_status');
        $results = $this->model_localisation_stock_status->get_descriptions_by_language_id($this->config->get('config_language_id'));
        foreach ($results as $stock_status) {
            $this->model_localisation_stock_status->add_description((int) $stock_status['stock_status_id'], $language_id, $stock_status);
        }
        // Subscription Plan
        $this->load->model('catalog/subscription_plan');
        $results = $this->model_catalog_subscription_plan->get_descriptions_by_language_id($this->config->get('config_language_id'));
        foreach ($results as $subscription_plan) {
            $this->model_catalog_subscription_plan->add_description((int) $subscription_plan['subscription_plan_id'], $language_id, $subscription_plan);
        }
        // Subscription Status
        $this->load->model('localisation/subscription_status');
        $results = $this->model_localisation_subscription_status->get_descriptions_by_language_id($this->config->get('config_language_id'));
        foreach ($results as $subscription) {
            $this->model_localisation_subscription_status->add_description((int) $subscription['subscription_status_id'], $language_id, $subscription);
        }
        // SEO
        $this->load->model('design/seo_url');
        $results = $this->model_design_seo_url->get_seo_urls_by_language_id($this->config->get('config_language_id'));
        foreach ($results as $seo_url) {
            $this->model_design_seo_url->add_seo_url($seo_url['key'], $seo_url['value'], $seo_url['keyword'], $seo_url['store_id'], $language_id, $seo_url['sort_order']);
        }
        // Setup new SEO URL language keyword
        $languages = $this->get_languages();
        foreach ($languages as $language) {
            // Set default store
            $this->model_design_seo_url->add_seo_url('language', (string) $data['code'], (string) $data['code'], 0, $language['language_id'], -2);
        }
        // Set default store
        $this->load->model('setting/store');
        $stores = $this->model_setting_store->get_stores();
        foreach ($stores as $store) {
            foreach ($languages as $language) {
                $this->model_design_seo_url->add_seo_url('language', (string) $data['code'], (string) $data['code'], $store['store_id'], $language['language_id'], -2);
            }
        }
        // Topic
        $this->load->model('cms/topic');
        $results = $this->model_cms_topic->get_descriptions_by_language_id($this->config->get('config_language_id'));
        foreach ($results as $topic) {
            $this->model_cms_topic->add_description((int) $topic['topic_id'], $language_id, $topic);
        }
        // Weight Class
        $this->load->model('localisation/weight_class');
        $results = $this->model_localisation_weight_class->get_descriptions_by_language_id($this->config->get('config_language_id'));
        foreach ($results as $weight_class) {
            $this->model_localisation_weight_class->add_description((int) $weight_class['weight_class_id'], $language_id, $weight_class);
        }
        // Zone
        $this->load->model('localisation/zone');
        $results = $this->model_localisation_zone->get_descriptions_by_language_id($this->config->get('config_language_id'));
        foreach ($results as $zone) {
            $this->model_localisation_zone->add_description((int) $zone['zone_id'], $language_id, $zone);
        }
        return $language_id;
    }
    /**
     * Edit Language
     *
     * Edit language record in the database.
     *
     * @param int                  $language_id primary key of the language record
     * @param array<string, mixed> $data        array of data
     *
     *
     * @example
     *
     * $language_data = [
     *     'name'       => 'Language Name',
     *     'code'       => 'Language Code',
     *     'locale'     => 'Language Locale',
     *     'extension'  => '',
     *     'sort_order' => 0,
     *     'status'     => 1
     * ];
     *
     * $this->load->model('localisation/language');
     *
     * $this->model_localisation_language->editLanguage($language_id, $language_data);
     */
    public function edit_language(int $language_id, array $data): void
    {
        $this->db->query('UPDATE `' . DB_PREFIX . "language` SET `name` = '" . $this->db->escape((string) $data['name']) . "', `code` = '" . $this->db->escape((string) $data['code']) . "', `locale` = '" . $this->db->escape((string) $data['locale']) . "', `extension` = '" . $this->db->escape((string) $data['extension']) . "', `sort_order` = '" . (int) $data['sort_order'] . "', `status` = '" . (bool) ($data['status'] ?? 0) . "' WHERE `language_id` = '" . $language_id . "'");
        $this->cache->delete('language');
    }
    /**
     * Delete Language
     *
     * Delete language record in the database.
     *
     * @param int $language_id primary key of the language record
     *
     *
     * @example
     *
     * $this->load->model('localisation/language');
     *
     * $this->model_localisation_language->deleteLanguage($language_id);
     */
    public function delete_language(int $language_id): void
    {
        $language_info = $this->get_language($language_id);
        $this->db->query('DELETE FROM `' . DB_PREFIX . "language` WHERE `language_id` = '" . $language_id . "'");
        $this->cache->delete('language');
        // Article
        $this->load->model('cms/article');
        $this->model_cms_article->delete_descriptions_by_language_id($language_id);
        // Attribute
        $this->load->model('catalog/attribute');
        $this->model_catalog_attribute->delete_descriptions_by_language_id($language_id);
        // Attribute Group
        $this->load->model('catalog/attribute_group');
        $this->model_catalog_attribute_group->delete_descriptions_by_language_id($language_id);
        // Banner
        $this->load->model('design/banner');
        $this->model_design_banner->delete_images_by_language_id($language_id);
        // Category
        $this->load->model('catalog/category');
        $this->model_catalog_category->delete_descriptions_by_language_id($language_id);
        // Country
        $this->load->model('localisation/country');
        $this->model_localisation_country->delete_descriptions_by_language_id($language_id);
        // Customer Group
        $this->load->model('customer/customer_group');
        $this->model_customer_customer_group->delete_descriptions_by_language_id($language_id);
        // Custom Field
        $this->load->model('customer/custom_field');
        $this->model_customer_custom_field->delete_descriptions_by_language_id($language_id);
        $this->model_customer_custom_field->delete_value_descriptions_by_language_id($language_id);
        // Download
        $this->load->model('catalog/download');
        $this->model_catalog_download->delete_descriptions_by_language_id($language_id);
        // Filter
        $this->load->model('catalog/filter');
        $this->model_catalog_filter->delete_descriptions_by_language_id($language_id);
        // Filter Group
        $this->load->model('catalog/filter_group');
        $this->model_catalog_filter_group->delete_descriptions_by_language_id($language_id);
        // Information
        $this->load->model('catalog/information');
        $this->model_catalog_information->delete_descriptions_by_language_id($language_id);
        // Length Class
        $this->load->model('localisation/length_class');
        $this->model_localisation_length_class->delete_descriptions_by_language_id($language_id);
        // Manufacturer
        $this->load->model('catalog/manufacturer');
        $this->model_catalog_manufacturer->delete_descriptions_by_language_id($language_id);
        // Option
        $this->load->model('catalog/option');
        $this->model_catalog_option->delete_descriptions_by_language_id($language_id);
        $this->model_catalog_option->delete_value_descriptions_by_language_id($language_id);
        // Order Status
        $this->load->model('localisation/order_status');
        $this->model_localisation_order_status->delete_order_statuses_by_language_id($language_id);
        // Product
        $this->load->model('catalog/product');
        $this->model_catalog_product->delete_descriptions_by_language_id($language_id);
        $this->model_catalog_product->delete_attributes_by_language_id($language_id);
        // Return Action
        $this->load->model('localisation/return_action');
        $this->model_localisation_return_action->delete_return_actions_by_language_id($language_id);
        // Return Reason
        $this->load->model('localisation/return_reason');
        $this->model_localisation_return_reason->delete_return_reasons_by_language_id($language_id);
        // Return Status
        $this->load->model('localisation/return_status');
        $this->model_localisation_return_status->delete_return_statuses_by_language_id($language_id);
        // Stock Status
        $this->load->model('localisation/stock_status');
        $this->model_localisation_stock_status->delete_stock_statuses_by_language_id($language_id);
        // Weight Class
        $this->load->model('localisation/weight_class');
        $this->model_localisation_weight_class->delete_descriptions_by_language_id($language_id);
        // Subscription Status
        $this->load->model('localisation/subscription_status');
        $this->model_localisation_subscription_status->delete_stock_statuses_by_language_id($language_id);
        // SEO
        $this->load->model('design/seo_url');
        $this->model_design_seo_url->delete_seo_urls_by_language_id($language_id);
        $this->model_design_seo_url->delete_seo_urls_by_key_value('language', $language_info['code']);
        // Topic Status
        $this->load->model('cms/topic');
        $this->model_cms_topic->delete_descriptions_by_language_id($language_id);
        // Zone
        $this->load->model('localisation/zone');
        $this->model_localisation_zone->delete_descriptions_by_language_id($language_id);
    }
    /**
     * Get Language
     *
     * Get the record of the language record in the database.
     *
     * @param int $language_id primary key of the language record
     *
     * @return array<string, mixed> language record that has language ID
     *
     * @example
     *
     * $this->load->model('localisation/language');
     *
     * $language_info = $this->model_localisation_language->getLanguage($language_id);
     */
    public function get_language(int $language_id): array
    {
        $query = $this->db->query('SELECT DISTINCT * FROM `' . DB_PREFIX . "language` WHERE `language_id` = '" . $language_id . "'");
        $language = $query->row;
        if ($language) {
            $language['image'] = HTTP_CATALOG;
            if (!$language['extension']) {
                $language['image'] .= 'catalog/';
            } else {
                $language['image'] .= 'extension/' . $language['extension'] . '/catalog/';
            }
            $language['image'] .= 'language/' . $language['code'] . '/' . $language['code'] . '.png';
        }
        return $language;
    }
    /**
     * Get Language By Code
     *
     *
     * @return array<string, mixed>
     * @example
     *
     * $this->load->model('localisation/language');
     *
     * $language_info = $this->model_localisation_language->getLanguageByCode($code);
     */
    public function get_language_by_code(string $code): array
    {
        $query = $this->db->query('SELECT * FROM `' . DB_PREFIX . "language` WHERE `code` = '" . $this->db->escape($code) . "'");
        $language = $query->row;
        if ($language) {
            $language['image'] = HTTP_CATALOG;
            if (!$language['extension']) {
                $language['image'] .= 'catalog/';
            } else {
                $language['image'] .= 'extension/' . $language['extension'] . '/catalog/';
            }
            $language['image'] .= 'language/' . $language['code'] . '/' . $language['code'] . '.png';
        }
        return $language;
    }
    /**
     * Get Languages
     *
     * Get the record of the language records in the database.
     *
     * @param array<string, mixed> $data array of filters
     *
     * @return array<string, array<string, mixed>> language records
     *
     * @example
     *
     * $filter_data = [
     *     'sort'  => 'name',
     *     'order' => 'DESC',
     *     'start' => 0,
     *     'limit' => 10
     * ];
     *
     * $this->load->model('localisation/language');
     *
     * $languages = $this->model_localisation_language->getLanguages($filter_data);
     */
    public function get_languages(array $data = []): array
    {
        $sql = 'SELECT * FROM `' . DB_PREFIX . 'language`';
        $sort_data = ['name', 'code', 'sort_order'];
        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= ' ORDER BY ' . $data['sort'];
        } else {
            $sql .= ' ORDER BY `sort_order`, `name`';
        }
        if (isset($data['order']) && $data['order'] == 'DESC') {
            $sql .= ' DESC';
        } else {
            $sql .= ' ASC';
        }
        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }
            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }
            $sql .= ' LIMIT ' . (int) $data['start'] . ',' . (int) $data['limit'];
        }
        $results = $this->cache->get('language.' . md5($sql));
        if (!$results) {
            $query = $this->db->query($sql);
            $results = $query->rows;
            $this->cache->set('language.' . md5($sql), $results);
        }
        $language_data = [];
        foreach ($results as $result) {
            $image = HTTP_CATALOG;
            if (!$result['extension']) {
                $image .= 'catalog/';
            } else {
                $image .= 'extension/' . $result['extension'] . '/catalog/';
            }
            $language_data[$result['language_id']] = $result + ['image' => $image . 'language/' . $result['code'] . '/' . $result['code'] . '.png'];
        }
        return $language_data;
    }
    /**
     * Get Languages By Extension
     *
     *
     * @return array<int, array<string, mixed>>
     * @example
     *
     * $this->load->model('localisation/language');
     *
     * $results = $this->model_localisation_language->getLanguagesByExtension($extension);
     */
    public function get_languages_by_extension(string $extension): array
    {
        $query = $this->db->query('SELECT * FROM `' . DB_PREFIX . "language` WHERE `extension` = '" . $this->db->escape($extension) . "'");
        return $query->rows;
    }
    /**
     * Get Total Languages
     *
     * Get the total number of language records in the database.
     *
     * @return int total number of language records
     *
     * @example
     *
     * $this->load->model('localisation/language');
     *
     * $language_total = $this->model_localisation_language->getTotalLanguages();
     */
    public function get_total_languages(): int
    {
        $query = $this->db->query('SELECT COUNT(*) AS `total` FROM `' . DB_PREFIX . 'language`');
        return (int) $query->row['total'];
    }
}