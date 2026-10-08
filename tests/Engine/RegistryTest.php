<?php

declare(strict_types=1);

use Opencart\System\Engine\Registry;
use PHPUnit\Framework\TestCase;

final class RegistryTest extends TestCase {
	public function testSetGetHasUnset(): void {
		$registry = new Registry();
		$obj = new stdClass();
		$registry->set('foo', $obj);
		$this->assertTrue($registry->has('foo'));
		$this->assertSame($obj, $registry->get('foo'));
		$this->assertSame($obj, $registry->foo);
		$this->assertTrue(isset($registry->foo));
		$registry->unset('foo');
		$this->assertFalse($registry->has('foo'));
		$this->assertNull($registry->get('foo'));
	}
}
