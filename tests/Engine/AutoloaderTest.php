<?php

declare(strict_types=1);

use Opencart\System\Engine\Autoloader;
use PHPUnit\Framework\TestCase;

final class AutoloaderTest extends TestCase {
	public function testLoadsSnakeCaseClassFile(): void {
		$dir = sys_get_temp_dir() . '/oc_auto_' . uniqid('', true) . '/';
		mkdir($dir, 0777, true);
		$file = $dir . 'cache_file.php';
		file_put_contents($file, "<?php\nnamespace OcAuto;\nclass CacheFile {}\n");

		$loader = new Autoloader();
		$loader->register('OcAuto', $dir, false);
		$this->assertTrue($loader->load('OcAuto\\CacheFile'));
		$this->assertTrue(class_exists('OcAuto\\CacheFile', false));
	}

	public function testUnknownClassReturnsFalse(): void {
		$loader = new Autoloader();
		$this->assertFalse($loader->load('NoSuch\\Class\\Here'));
	}

	public function testPsr4KeepsCase(): void {
		$dir = sys_get_temp_dir() . '/oc_psr4_' . uniqid('', true) . '/';
		mkdir($dir . 'Sub', 0777, true);
		file_put_contents($dir . 'Sub/MyClass.php', "<?php\nnamespace Psr4Test\\Sub;\nclass MyClass {}\n");

		$loader = new Autoloader();
		$loader->register('Psr4Test', $dir, true);
		$this->assertTrue($loader->load('Psr4Test\\Sub\\MyClass'));
		$this->assertTrue(class_exists('Psr4Test\\Sub\\MyClass', false));
	}
}
