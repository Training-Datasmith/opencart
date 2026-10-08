<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class BbcodeTest extends TestCase {
	public function testInlineTags(): void {
		$this->assertSame('<strong>x</strong>', oc_bbcode_decode('[b]x[/b]'));
		$this->assertSame('<em>x</em>', oc_bbcode_decode('[i]x[/i]'));
		$this->assertSame('<u>x</u>', oc_bbcode_decode('[u]x[/u]'));
		$this->assertSame('<s>x</s>', oc_bbcode_decode('[s]x[/s]'));
		$this->assertSame('<code>x</code>', oc_bbcode_decode('[code]x[/code]'));
		$this->assertSame('<blockquote>x</blockquote>', oc_bbcode_decode('[quote]x[/quote]'));
	}

	public function testLinksListsAndColor(): void {
		$html = oc_bbcode_decode('[url=https://example.com]link[/url]');
		$this->assertStringContainsString('href="https://example.com"', $html);
		$this->assertStringContainsString('link', $html);

		$list = oc_bbcode_decode('[list][*]one[/list]');
		$this->assertStringContainsString('<ul>', $list);
		$this->assertStringContainsString('<li>one</li>', $list);

		$color = oc_bbcode_decode('[color=#fff]x[/color]');
		$this->assertStringContainsString('color: #fff', $color);

		$yt = oc_bbcode_decode('[youtube]abc123[/youtube]');
		$this->assertStringContainsString('abc123', $yt);
		$this->assertStringContainsString('<iframe', $yt);
	}
}
