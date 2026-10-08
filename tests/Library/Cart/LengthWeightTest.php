<?php

declare(strict_types=1);

use Opencart\System\Library\Cart\Length;
use Opencart\System\Library\Cart\Weight;
use PHPUnit\Framework\TestCase;
use Tests\Support\CartFixtures;

final class LengthWeightTest extends TestCase {
	public function testLengthConvertFormatUnit(): void {
		$registry = CartFixtures::registryWithDb();
		CartFixtures::ensureLocalizationTables($registry->get('db'));
		$length = new Length($registry);
		$this->assertSame(10.0, $length->convert(1, 1, 2));
		$this->assertSame(1.0, $length->convert(10, 2, 1));
		$this->assertSame(5.0, $length->convert(5, 1, 1));
		$this->assertSame('1.50cm', $length->format(1.5, 1));
		$this->assertSame('mm', $length->getUnit(2));
		$this->assertSame('', $length->getUnit(99));
	}

	public function testWeightConvertFormatUnit(): void {
		$registry = CartFixtures::registryWithDb();
		CartFixtures::ensureLocalizationTables($registry->get('db'));
		$weight = new Weight($registry);
		$this->assertSame(1000.0, $weight->convert(1, 1, 2));
		$this->assertEqualsWithDelta(2.2046, $weight->convert(1, 1, 3), 0.0001);
		$this->assertSame('2.00kg', $weight->format(2, 1));
	}
}
