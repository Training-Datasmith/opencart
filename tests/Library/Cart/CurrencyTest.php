<?php

declare(strict_types=1);

use Opencart\System\Engine\Registry;
use Opencart\System\Library\Cart\Currency;
use Opencart\System\Library\Language;
use PHPUnit\Framework\TestCase;
use Tests\Support\CartFixtures;

final class CurrencyTest extends TestCase {
	private function registryWithLanguage(): Registry {
		$registry = CartFixtures::registryWithDb();
		CartFixtures::ensureLocalizationTables($registry->get('db'));
		$language = new Language('en-gb');
		$language->set('decimal_point', '.');
		$language->set('thousand_point', ',');
		$registry->set('language', $language);

		return $registry;
	}

	public function testConvertAndFormat(): void {
		$currency = new Currency($this->registryWithLanguage());
		$this->assertEqualsWithDelta(8.0, $currency->convert(10, 'USD', 'EUR'), 0.0001);
		$this->assertEqualsWithDelta(10.04, $currency->format(1.004, 'USD', 10, false), 0.0001);
		$this->assertSame('$10.04', $currency->format(1.004, 'USD', 10, true));
		$this->assertSame('', $currency->format(1, 'ZZZ'));
	}

	public function testAccessors(): void {
		$currency = new Currency($this->registryWithLanguage());
		$this->assertTrue($currency->has('USD'));
		$this->assertSame(1, $currency->getId('USD'));
		$this->assertSame('$', $currency->getSymbolLeft('USD'));
		$this->assertSame('€', $currency->getSymbolRight('EUR'));
		$this->assertSame(2, $currency->getDecimalPlace('USD'));
		$this->assertEqualsWithDelta(1.0, $currency->getValue('USD'), 0.0001);
		$this->assertSame(0, $currency->getId('ZZZ'));
	}
}
