<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Tests\Support\ServerReset;

final class GeneralTest extends TestCase {
	use ServerReset;

	protected function setUp(): void {
		$this->setUpServerReset();
	}

	protected function tearDown(): void {
		$this->tearDownServerReset();
	}

	public function testTokenLengthAndHex(): void {
		$token = oc_token(16);
		$this->assertSame(16, strlen($token));
		$this->assertMatchesRegularExpression('/^[0-9a-f]+$/', $token);
	}

	public function testMultibyteStringHelpers(): void {
		$this->assertSame(5, oc_strlen('héllo'));
		$this->assertSame(2, oc_strpos('héllo', 'l'));
		$this->assertSame('él', oc_substr('héllo', 1, 2));
		$this->assertSame('É', oc_strtoupper('é'));
	}

	public function testValidateLengthTrims(): void {
		$this->assertTrue(oc_validate_length('  ab  ', 2, 2));
		$this->assertFalse(oc_validate_length('a', 2, 4));
	}

	public function testValidateEmail(): void {
		$this->assertTrue(oc_validate_email('user@example.com'));
		$this->assertFalse(oc_validate_email(str_repeat('a', 90) . '@example.com'));
		$this->assertFalse(oc_validate_email('not-an-email'));
	}

	public function testValidateRegexIpUrl(): void {
		$this->assertTrue(oc_validate_regex('abc', '/^[a-z]+$/'));
		$this->assertFalse(oc_validate_regex('ab1', '/^[a-z]+$/'));
		$this->assertTrue(oc_validate_ip('127.0.0.1'));
		$this->assertFalse(oc_validate_ip('999.1.1.1'));
		$this->assertTrue(oc_validate_url('https://shop.example/path'));
		$this->assertFalse(oc_validate_url('not a url'));
	}

	public function testValidateFilenameAndPath(): void {
		$this->assertTrue(oc_validate_filename('photo.jpg'));
		$this->assertTrue(oc_validate_filename('файл.jpg'));
		$this->assertFalse(oc_validate_filename('a/b.jpg'));
		$this->assertTrue(oc_validate_path('café/menu'));
		$this->assertFalse(oc_validate_path('bad slug'));
	}

	public function testGetIpHeaderOrder(): void {
		$_SERVER['HTTP_CF_CONNECTING_IP'] = '10.1.2.3';
		$_SERVER['HTTP_X_FORWARDED_FOR'] = '10.9.8.7, 10.4.5.6';
		$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
		$this->assertSame('10.1.2.3', oc_get_ip());

		unset($_SERVER['HTTP_CF_CONNECTING_IP']);
		$this->assertSame('10.9.8.7', oc_get_ip());

		unset($_SERVER['HTTP_X_FORWARDED_FOR']);
		$this->assertSame('127.0.0.1', oc_get_ip());
	}

	public function testFileRoundTrip(): void {
		$file = sys_get_temp_dir() . '/oc_general_' . uniqid('', true) . '.txt';
		$this->assertTrue(oc_file_write($file, 'hello'));
		$this->assertTrue(oc_file_write($file, ' world', true));
		$this->assertSame('hello world', oc_file_read($file));
		$this->assertTrue(oc_file_delete($file));
		$this->assertFalse(oc_file_read($file));
	}

	public function testDirectoryReadCreateDelete(): void {
		$base = sys_get_temp_dir() . '/oc_dir_' . uniqid('', true);
		$this->assertTrue(oc_directory_create($base . '/sub'));
		file_put_contents($base . '/sub/a.txt', 'a');
		file_put_contents($base . '/sub/b.txt', 'b');

		$resolved = realpath($base);
		$this->assertIsString($resolved);
		$this->assertNotSame('', $resolved);

		$files = oc_directory_read($resolved, true, '/a\.txt$/');
		$this->assertContains($resolved . '/sub/a.txt', $files);
		$this->assertNotContains($resolved . '/sub/b.txt', $files);

		$recursive = oc_directory_read($resolved, true);
		$this->assertContains($resolved . '/sub/a.txt', $recursive);

		$this->assertTrue(oc_directory_delete($resolved));
		$this->assertSame([], oc_directory_read($base));
		$this->assertSame([], oc_directory_read($base . '/missing'));
		$this->assertSame([], oc_directory_read($file = $base . '/notadir'));
	}
}
