<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class YamlTest extends TestCase {
	public function testDecodeFixedDocument(): void {
		$yaml = "name: Ada\n" . "count: 2\n";
		$this->assertSame(['name' => 'Ada', 'count' => 2], oc_yaml_decode($yaml));
	}

	public function testEncodeRoundTrip(): void {
		$value = ['name' => 'Ada', 'count' => 2];
		$this->assertSame($value, oc_yaml_decode(oc_yaml_encode($value)));
	}
}
