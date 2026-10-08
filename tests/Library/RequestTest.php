<?php

declare(strict_types=1);

use Opencart\System\Library\Request;
use PHPUnit\Framework\TestCase;

final class RequestTest extends TestCase {
	public function testCleanEncodesMarkup(): void {
		$_GET = ['q' => '<b onclick="x">'];
		$request = new Request();
		$this->assertSame('&lt;b onclick=&quot;x&quot;&gt;', $request->get['q']);
	}

	public function testTypedGetters(): void {
		$_GET = ['name' => 'Ada', 'count' => '3'];
		$request = new Request();
		$this->assertNull($request->get('missing'));
		$this->assertSame('', $request->get('missing', 'string'));
		$this->assertSame(0, $request->get('missing', 'int'));
		$this->assertSame('Ada', $request->get('name', 'string'));
		$this->assertSame(3, $request->get('count', 'int'));
	}

	public function testFilesIntegersAreCleaned(): void {
		$_FILES = ['file' => ['error' => 0, 'size' => 123]];
		$request = new Request();
		$this->assertSame('0', $request->files['file']['error']);
		$this->assertSame('123', $request->files['file']['size']);
	}
}
