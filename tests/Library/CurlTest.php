<?php

declare(strict_types=1);

use Opencart\System\Library\Curl;
use PHPUnit\Framework\TestCase;

final class CurlTest extends TestCase {
	private string $docRoot;
	private ?string $baseUrl = null;
	/** @var resource|null */
	private $serverProcess = null;
	/** @var array<int, resource> */
	private array $serverPipes = [];

	protected function setUp(): void {
		$this->docRoot = sys_get_temp_dir() . '/oc_curl_' . uniqid('', true);
		mkdir($this->docRoot, 0777, true);
		file_put_contents($this->docRoot . '/router.php', <<<'PHP'
<?php
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($path === '/ok') {
	header('Content-Type: application/json');
	echo '{"ok":true,"n":2}';
	return true;
}
if ($path === '/bad') {
	header('Content-Type: text/plain');
	echo 'not-json';
	return true;
}
http_response_code(500);
header('Content-Type: application/json');
echo '{"ok":false}';
return true;
PHP
		);
		$port = 0;
		$socket = stream_socket_server('tcp://127.0.0.1:0', $errno, $errstr);
		$this->assertIsResource($socket);
		$address = stream_socket_get_name($socket, false);
		fclose($socket);
		[, $port] = explode(':', $address);
		$this->baseUrl = 'http://127.0.0.1:' . $port;
		$descriptors = [
			0 => ['pipe', 'r'],
			1 => ['pipe', 'w'],
			2 => ['pipe', 'w'],
		];
		$this->serverProcess = proc_open(
			[
				'php',
				'-S',
				'127.0.0.1:' . $port,
				'-t',
				$this->docRoot,
				$this->docRoot . '/router.php',
			],
			$descriptors,
			$pipes
		);
		$this->assertIsResource($this->serverProcess);
		$this->serverPipes = $pipes;
		if (isset($pipes[0])) {
			fclose($pipes[0]);
		}
		stream_set_blocking($pipes[1], false);
		stream_set_blocking($pipes[2], false);
		$ready = false;
		for ($i = 0; $i < 100; $i++) {
			$fp = @fsockopen('127.0.0.1', (int)$port, $errno, $errstr, 0.2);
			if ($fp) {
				fclose($fp);
				$ready = true;
				break;
			}
			usleep(50000);
		}
		$this->assertTrue($ready, 'Built-in HTTP server did not become ready');
	}

	protected function tearDown(): void {
		foreach ($this->serverPipes as $pipe) {
			if (is_resource($pipe)) {
				fclose($pipe);
			}
		}
		$this->serverPipes = [];
		if (is_resource($this->serverProcess)) {
			proc_terminate($this->serverProcess);
			proc_close($this->serverProcess);
		}
		if (is_dir($this->docRoot)) {
			unlink($this->docRoot . '/router.php');
			rmdir($this->docRoot);
		}
	}

	public function testClassAutoloadsInSystemLibrary(): void {
		$this->assertTrue(class_exists(Curl::class));
	}

	public function testJson200Response(): void {
		$curl = new Curl();
		$curl->setOption(CURLOPT_POST, false);
		$result = $curl->send($this->baseUrl . '/ok', []);
		$this->assertSame(['ok' => true, 'n' => 2], $result);
	}

	public function testNonJson200ReturnsEmptyArray(): void {
		$curl = new Curl();
		$curl->setOption(CURLOPT_POST, false);
		$this->assertSame([], $curl->send($this->baseUrl . '/bad', []));
	}

	public function testHttp500ReturnsEmptyArray(): void {
		$curl = new Curl();
		$curl->setOption(CURLOPT_POST, false);
		$this->assertSame([], $curl->send($this->baseUrl . '/missing', []));
	}
}
