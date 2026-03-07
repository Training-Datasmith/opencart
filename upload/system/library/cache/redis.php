<?php
namespace Opencart\System\Library\Cache;
/**
 * Class Redis
 *
 * @package Opencart\System\Library\Cache
 */
class Redis {
	private \Redis $redis;

	/**
     * Constructor
     */
    public function __construct(private int $expire = 3600) {
		$this->redis = new \Redis();
		$this->redis->pconnect(CACHE_HOSTNAME, CACHE_PORT);
	}

	/**
     * Get
     *
     *
     */
    public function get(string $key): mixed {
		$data = $this->redis->get(CACHE_PREFIX . $key);

		return json_decode($data, true);
	}

	/**
     * Set
     *
     * @param mixed  $value
     *
     */
    public function set(string $key, $value, int $expire = 0): void {
		if (!$expire) {
			$expire = $this->expire;
		}

		$status = $this->redis->set(CACHE_PREFIX . $key, json_encode($value));

		if ($status) {
			$this->redis->expire(CACHE_PREFIX . $key, $expire);
		}
	}

	/**
     * Delete
     *
     *
     */
    public function delete(string $key): void {
		$this->redis->del(CACHE_PREFIX . $key);
	}
}
