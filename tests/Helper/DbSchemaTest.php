<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Tests\Support\MysqlConfig;

final class DbSchemaTest extends TestCase {
	protected function setUp(): void {
		$admin = MysqlConfig::adminConnection();
		$schema = getenv('OC_DB_SCHEMA_DATABASE') ?: 'oc_schema';
		$admin->query('CREATE DATABASE IF NOT EXISTS `' . $admin->real_escape_string($schema) . '`');
		$admin->close();
	}

	public function testOcDbCreateSucceeds(): void {
		$schema = getenv('OC_DB_SCHEMA_DATABASE') ?: 'oc_schema';
		$ok = oc_db_create(
			'mysqli',
			getenv('OC_DB_HOSTNAME') ?: '127.0.0.1',
			getenv('OC_DB_USERNAME') ?: 'root',
			getenv('OC_DB_PASSWORD') ?: 'opencart',
			$schema,
			getenv('OC_DB_PORT') ?: '3306',
			DB_PREFIX,
			'',
			'',
			''
		);
		$this->assertTrue($ok);
		$admin = MysqlConfig::adminConnection();
		$result = $admin->query("SHOW TABLES IN `{$schema}` LIKE '" . DB_PREFIX . "currency'");
		$this->assertSame(1, $result->num_rows);
		$admin->close();
	}

	public function testOcDbCreateInvalidEngineReturnsFalse(): void {
		$schema = getenv('OC_DB_SCHEMA_DATABASE') ?: 'oc_schema';
		$this->assertFalse(oc_db_create(
			'nope',
			getenv('OC_DB_HOSTNAME') ?: '127.0.0.1',
			getenv('OC_DB_USERNAME') ?: 'root',
			getenv('OC_DB_PASSWORD') ?: 'opencart',
			$schema,
			getenv('OC_DB_PORT') ?: '3306',
			DB_PREFIX,
			'',
			'',
			''
		));
	}
}
