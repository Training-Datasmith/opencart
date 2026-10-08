<?php

declare(strict_types=1);

use Opencart\System\Library\Cart\Tax;
use PHPUnit\Framework\TestCase;
use Tests\Support\CartFixtures;

final class TaxTest extends TestCase {
	public function testShippingTaxCalculation(): void {
		$registry = CartFixtures::registryWithDb();
		CartFixtures::ensureLocalizationTables($registry->get('db'));
		$tax = new Tax($registry);
		$tax->setShippingAddress(1, 2);
		$this->assertSame(122.0, $tax->calculate(100, 9, true));
		$this->assertSame(22.0, $tax->getTax(100, 9));
		$rates = $tax->getRates(100, 9);
		$this->assertSame(20.0, $rates[86]['amount']);
		$this->assertSame(2.0, $rates[87]['amount']);
		$this->assertSame(100.0, $tax->calculate(100, 9, false));

		$tax->clear();
		$tax->setShippingAddress(99, 99);
		$this->assertSame(0.0, $tax->getTax(100, 9));

		$registry2 = CartFixtures::registryWithDb();
		CartFixtures::ensureLocalizationTables($registry2->get('db'));
		$tax2 = new Tax($registry2);
		$tax2->setShippingAddress(1, 2);
		$this->assertSame('VAT', $tax2->getRateName(86));
		$this->assertFalse($tax2->getRateName(9999));
	}
}
