<?php

declare(strict_types=1);

use Opencart\System\Engine\Action;
use Opencart\System\Engine\Config;
use Opencart\System\Engine\Event;
use Opencart\System\Engine\Factory;
use Opencart\System\Engine\Registry;
use Opencart\System\Engine\Autoloader;
use PHPUnit\Framework\TestCase;

final class EventActionTest extends TestCase {
	private function registry(): Registry {
		$registry = new Registry();
		$config = new Config();
		$config->set('application', 'Test');
		$registry->set('config', $config);
		$autoloader = new Autoloader();
		$autoloader->register('Opencart\Test', dirname(__DIR__) . '/fixtures/');
		$registry->set('autoloader', $autoloader);
		$registry->set('factory', new Factory($registry));
		$registry->set('event', new Event($registry));

		return $registry;
	}

	public function testGetIdStripsIllegalCharacters(): void {
		$action = new Action('tool/probe.index<script>');
		$this->assertSame('tool/probe.indexscript', $action->getId());
	}

	public function testMagicMethodRejected(): void {
		$registry = $this->registry();
		$action = new Action('tool/probe.__construct');
		$result = $action->execute($registry);
		$this->assertInstanceOf(Exception::class, $result);
	}

	public function testUnknownController(): void {
		$registry = $this->registry();
		$action = new Action('tool/missing.index');
		$result = $action->execute($registry);
		$this->assertInstanceOf(Exception::class, $result);
	}

	public function testTriggerRunsInPriorityOrder(): void {
		$registry = $this->registry();
		$event = $registry->get('event');
		$marker = sys_get_temp_dir() . '/oc_event_' . uniqid('', true);
		file_put_contents($marker, '');

		$event->register('run/*', new Action('tool/probe.second'), 10);
		$event->register('run/*', new Action('tool/probe.first'), 1);
		$event->trigger('run/before', [&$marker]);
		$this->assertSame('firstsecond', file_get_contents($marker));

		$marker2 = sys_get_temp_dir() . '/oc_event2_' . uniqid('', true);
		file_put_contents($marker2, '');
		$event->trigger('other/event', [&$marker2]);
		$this->assertSame('', file_get_contents($marker2));
	}

	public function testWildcardUnregisterAndClear(): void {
		$registry = $this->registry();
		$event = $registry->get('event');
		$marker = sys_get_temp_dir() . '/oc_event3_' . uniqid('', true);
		file_put_contents($marker, '');

		$event->register('catalog/product/*', new Action('tool/probe.index'), 0);
		$event->trigger('catalog/product/before', [&$marker]);
		$this->assertSame('index', file_get_contents($marker));

		$event->unregister('catalog/product/*', 'tool/probe.index');
		file_put_contents($marker, '');
		$event->trigger('catalog/product/before', [&$marker]);
		$this->assertSame('', file_get_contents($marker));

		$event->register('catalog/product/*', new Action('tool/probe.index'), 0);
		$event->clear('catalog/product/*');
		$event->trigger('catalog/product/before', [&$marker]);
		$this->assertSame('', file_get_contents($marker));
	}
}
