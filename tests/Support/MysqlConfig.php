<?php

declare(strict_types=1);

namespace Tests\Support;

final class MysqlConfig {
	public static function options(string $database = ''): array {
		return [
			'engine'   => 'mysqli',
			'hostname' => getenv('OC_DB_HOSTNAME') ?: '127.0.0.1',
			'username' => getenv('OC_DB_USERNAME') ?: 'root',
			'password' => getenv('OC_DB_PASSWORD') ?: 'opencart',
			'database' => $database !== '' ? $database : (getenv('OC_DB_DATABASE') ?: 'opencart'),
			'port'     => getenv('OC_DB_PORT') ?: '3306',
			'ssl_key'  => '',
			'ssl_cert' => '',
			'ssl_ca'   => ''
		];
	}

	public static function adminConnection(): \mysqli {
		$options = self::options();
		$mysqli = new \mysqli(
			$options['hostname'],
			$options['username'],
			$options['password'],
			'',
			(int)$options['port']
		);

		if ($mysqli->connect_error) {
			throw new \RuntimeException('MySQL connection failed: ' . $mysqli->connect_error);
		}

		return $mysqli;
	}
}
