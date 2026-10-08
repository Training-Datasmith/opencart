<?php

declare(strict_types=1);

use Opencart\System\Library\Language;
use Opencart\System\Library\Log;
use PHPUnit\Framework\TestCase;

final class LogLanguageTest extends TestCase {
	public function testLogWriteFormat(): void {
		$log = new Log('test.log');
		$log->write('hello');
		$contents = file_get_contents(DIR_LOGS . 'test.log');
		$this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2} - /', $contents);
		$this->assertStringContainsString('hello', $contents);
	}

	public function testLanguageLoadAndPrefix(): void {
		$dir = sys_get_temp_dir() . '/oc_lang_' . uniqid('', true) . '/';
		mkdir($dir . 'en-gb/test', 0777, true);
		file_put_contents($dir . 'en-gb/test/sample.php', "<?php\n\$_['heading_title'] = 'Hello';\n");

		$language = new Language('en-gb');
		$language->addPath($dir);
		$this->assertSame('missing_key', $language->get('missing_key'));
		$language->set('custom', 'value');
		$this->assertSame('value', $language->get('custom'));
		$language->load('test/sample');
		$this->assertSame('Hello', $language->get('heading_title'));
		$language->load('test/sample', 'heading');
		$this->assertSame('Hello', $language->get('heading_title'));
		$prefixed = $language->all('heading');
		$this->assertArrayHasKey('title', $prefixed);
		$language->clear();
		$this->assertSame('heading_title', $language->get('heading_title'));
	}
}
