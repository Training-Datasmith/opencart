<?php

declare(strict_types=1);

use Opencart\System\Engine\Config;
use PHPUnit\Framework\TestCase;

final class ConfigTest extends TestCase {
	public function testSetGetHas(): void {
		$config = new Config();
		$config->set('zero', 0);
		$this->assertSame(0, $config->get('zero'));
		$this->assertTrue($config->has('zero'));
		$this->assertSame('', $config->get('missing'));
		$this->assertFalse($config->has('missing'));
	}

	public function testLoadMergesFile(): void {
		$dir = sys_get_temp_dir() . '/oc_cfg_' . uniqid('', true) . '/';
		mkdir($dir, 0777, true);
		$subdir = $dir . 'extra/';
		mkdir($subdir, 0777, true);

		file_put_contents($dir . 'app.php', "<?php\n\$_['app_name'] = 'Shop';\n");
		file_put_contents($subdir . 'app.php', "<?php\n\$_['app_name'] = 'Override';\n");

		$config = new Config();
		$config->set('keep', 'yes');
		$config->addPath($dir);
		$config->load('app');
		$this->assertSame('Shop', $config->get('app_name'));
		$this->assertSame('yes', $config->get('keep'));

		$config->addPath('extra', $subdir);
		$config->load('extra/app');
		$this->assertSame('Override', $config->get('app_name'));

		$config->load('missing');
		$this->assertSame('Override', $config->get('app_name'));
	}

	public function testLoadRealDefault(): void {
		$config = new Config();
		$config->addPath(DIR_CONFIG);
		$config->load('default');
		$this->assertSame('en-gb', $config->get('language_code'));
		$this->assertSame('UTC', $config->get('date_timezone'));
	}
}
