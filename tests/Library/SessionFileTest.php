<?php

declare(strict_types=1);

use Opencart\System\Engine\Config;
use Opencart\System\Engine\Registry;
use Opencart\System\Library\Session\File;
use PHPUnit\Framework\TestCase;

final class SessionFileTest extends TestCase {
	private function sessionFile(): File {
		$registry = new Registry();
		$config = new Config();
		$config->set('session_divisor', 1000);
		$config->set('session_probability', 1);
		$config->set('session_expire', 3600);
		$registry->set('config', $config);

		return new File($registry);
	}

	public function testRoundTripAndDestroy(): void {
		$session = $this->sessionFile();
		$id = 'abcdefghijklmnopqrstuvwx';
		$this->assertTrue($session->write($id, ['customer_id' => 4]));
		$this->assertSame(['customer_id' => 4], $session->read($id));
		$session->destroy($id);
		$this->assertSame([], $session->read($id));
	}

	public function testCorruptAndEmptyFilesReturnArray(): void {
		$session = $this->sessionFile();
		$id = 'zyxwvutsrqponmlkjihgfedcba';
		$file = DIR_SESSION . 'sess_' . $id;
		file_put_contents($file, 'not-json');
		$this->assertSame([], $session->read($id));
		file_put_contents($file, '');
		$this->assertSame([], $session->read($id));
	}

	public function testReadUsesBasenameOnly(): void {
		$session = $this->sessionFile();
		$secret = 'secret';
		file_put_contents(DIR_SESSION . 'sess_' . $secret, json_encode(['ok' => 1]));
		$this->assertSame(['ok' => 1], $session->read('../' . $secret));
	}
}
