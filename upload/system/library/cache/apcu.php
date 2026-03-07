<?php

declare(strict_types=1);

namespace Opencart\System\Library\Cache;

/**
 * Class APCU
 *
 * @package Opencart\System\Library\Cache
 */
class Apcu
{
    private bool $active;

    /**
     * Constructor
     */
    public function __construct(private int $expire = 3600)
    {
        $this->active = function_exists('apcu_cache_info') && ini_get('apc.enabled');
    }

    /**
     * Get
     *
     *
     * @return mixed
     */
    public function get(string $key)
    {
        return $this->active ? apcu_fetch(CACHE_PREFIX . $key) : [];
    }

    /**
     * Set
     *
     * @param mixed  $value
     *
     */
    public function set(string $key, $value, int $expire = 0): void
    {
        if (!$expire) {
            $expire = $this->expire;
        }

        if ($this->active) {
            apcu_store(CACHE_PREFIX . $key, $value, $expire);
        }
    }

    /**
     * Delete
     *
     *
     */
    public function delete(string $key): void
    {
        if ($this->active) {
            $cache_info = apcu_cache_info();

            $cache_list = $cache_info['cache_list'];

            foreach ($cache_list as $entry) {
                if (str_starts_with($entry['info'], CACHE_PREFIX . $key)) {
                    apcu_delete($entry['info']);
                }
            }
        }
    }

    /**
     * Delete all cache
     */
    public function flush(): bool
    {
        if (function_exists('apcu_clear_cache')) {
            return apcu_clear_cache();
        }

        return false;
    }
}
