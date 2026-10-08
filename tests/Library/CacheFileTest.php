<?php

declare(strict_types=1);

use Opencart\System\Library\Cache\File;
use PHPUnit\Framework\TestCase;

final class CacheFileTest extends TestCase {
	public function testSetGetDeleteAndExpiry(): void {
		$cache = new File(3600);
		$cache->set('alpha', ['n' => 1]);
		$this->assertSame(['n' => 1], $cache->get('alpha'));
		$cache->set('zero', 0);
		$this->assertSame(0, $cache->get('zero'));
		$cache->delete('alpha');
		$this->assertSame([], $cache->get('alpha'));

		$key = 'expired';
		$sanitized = preg_replace('/[^A-Z0-9\._-]/i', '', $key);
		$expiredFile = DIR_CACHE . 'cache.' . $sanitized . '.' . (time() - 10);
		file_put_contents($expiredFile, json_encode(['x' => 1]));
		$this->assertSame([], $cache->get($key));
		$this->assertFileDoesNotExist($expiredFile);

		$futureFile = DIR_CACHE . 'cache.' . $sanitized . '_future.' . (time() + 3600);
		file_put_contents($futureFile, json_encode(['y' => 2]));
		$this->assertSame(['y' => 2], $cache->get($sanitized . '_future'));
	}

	public function testKeySanitizationStaysInCacheDir(): void {
		$cache = new File(3600);
		$cache->set('../evil', ['bad' => true]);
		$cacheRoot = realpath(DIR_CACHE);
		$this->assertIsString($cacheRoot);
		foreach (glob(DIR_CACHE . 'cache.*') as $file) {
			$resolved = realpath($file);
			$this->assertIsString($resolved);
			$this->assertStringStartsWith($cacheRoot, $resolved);
		}
	}
}
