<?php

declare(strict_types=1);

namespace Opencart\System\Library\Cache;

/**
 * Class Mem
 *
 * @package Opencart\System\Library\Cache
 */
class Mem
{
    private \Memcache $memcache;

    public const CACHEDUMP_LIMIT = 9999;

    /**
     * Constructor
     */
    public function __construct(private int $expire = 3600)
    {
        $this->memcache = new \Memcache();
        $this->memcache->pconnect(CACHE_HOSTNAME, CACHE_PORT);
    }

    /**
     * Get
     *
     *
     */
    public function get(string $key): mixed
    {
        return $this->memcache->get(CACHE_PREFIX . $key);
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

        $this->memcache->set(CACHE_PREFIX . $key, $value, MEMCACHE_COMPRESSED, $expire);
    }

    /**
     * Delete
     *
     *
     */
    public function delete(string $key): void
    {
        $this->memcache->delete(CACHE_PREFIX . $key);
    }
}
