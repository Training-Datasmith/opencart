<?php

declare(strict_types=1);

use Opencart\System\Library\Url;
use PHPUnit\Framework\TestCase;

final class UrlTest extends TestCase {
	public function testLinkEscapesAmpersandForHtml(): void {
		$url = new Url('https://shop.example/');
		$link = $url->link('product/product', 'product_id=5&search=hat');
		$this->assertStringContainsString('&amp;', $link);
		$this->assertStringContainsString('product_id=5', $link);
	}

	public function testLinkJsModeKeepsAmpersand(): void {
		$url = new Url('https://shop.example/');
		$link = $url->link('product/product', 'product_id=5', true);
		$this->assertStringNotContainsString('&amp;', $link);
		$this->assertStringContainsString('&product_id=5', $link);
	}

	public function testLinkArrayArgsAndRewrite(): void {
		$url = new Url('https://shop.example/');
		$link = $url->link('checkout/cart', ['product_id' => 5]);
		$this->assertStringContainsString('product_id=5', $link);

		$rewrite = new class() {
			public function rewrite(string $link): string {
				return str_replace('index.php?route=', 'pretty/', $link);
			}
		};
		$url->addRewrite($rewrite);
		$pretty = $url->link('product/product', 'q=%3F');
		$this->assertStringContainsString('?', $pretty);
		$this->assertStringContainsString('pretty/', $pretty);
	}
}
