<?php

declare (strict_types=1);
namespace Opencart\System\Library\Cache;

/**
 * Class Memcached
 *
 * @package Opencart\System\Library\Cache
 */
class Memcached
{
    private \Memcached $memcached;
    public const CACHEDUMP_LIMIT = 9999;
    /**
     * Constructor
     */
    public function __construct(private int $expire = 3600)
    {
        $this->memcached = new \Memcached();
        $this->memcached->add_server(CACHE_HOSTNAME, CACHE_PORT);
    }
    /**
     * Get
     *
     *
     */
    public function get(string $key): mixed
    {
        return $this->memcached->get(CACHE_PREFIX . $key);
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
        $this->memcached->set(CACHE_PREFIX . $key, $value, $expire);
    }
    /**
     * Delete
     *
     *
     */
    public function delete(string $key): void
    {
        $this->memcached->delete(CACHE_PREFIX . $key);
    }
}