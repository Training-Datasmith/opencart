<?php

declare(strict_types=1);

namespace Tests\Support;

trait ServerReset {
	/** @var array<string, mixed> */
	private array $savedServer = [];

	protected function setUpServerReset(): void {
		$this->savedServer = $_SERVER;
	}

	protected function tearDownServerReset(): void {
		$_SERVER = $this->savedServer;
	}
}
