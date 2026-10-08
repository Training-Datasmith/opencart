<?php

declare(strict_types=1);

use Opencart\System\Library\DB;
use PHPUnit\Framework\TestCase;
use Tests\Support\MysqlConfig;

final class DbMysqliTest extends TestCase {
	private DB $db;

	protected function setUp(): void {
		$this->db = new DB(MysqlConfig::options());
		$p = DB_PREFIX;
		$this->db->query("DROP TABLE IF EXISTS `{$p}test_probe`");
		$this->db->query("CREATE TABLE `{$p}test_probe` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`name` varchar(64) NOT NULL,
			PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
	}

	protected function tearDown(): void {
		$p = DB_PREFIX;
		$this->db->query("DROP TABLE IF EXISTS `{$p}test_probe`");
	}

	public function testQueryEscapeAndMetadata(): void {
		$p = DB_PREFIX;
		$name = "O'Brien";
		$this->db->query("INSERT INTO `{$p}test_probe` SET `name` = '" . $this->db->escape($name) . "'");
		$this->assertTrue($this->db->isConnected());
		$id = $this->db->getLastId();
		$this->assertGreaterThan(0, $id);
		$result = $this->db->query("SELECT * FROM `{$p}test_probe` WHERE `id` = '" . (int)$id . "'");
		$this->assertSame(1, $result->num_rows);
		$this->assertSame($name, $result->row['name']);
		$this->assertCount(1, $result->rows);
		$this->db->query("UPDATE `{$p}test_probe` SET `name` = 'Updated' WHERE `id` = '" . (int)$id . "'");
		$this->assertSame(1, $this->db->countAffected());
	}

	public function testConstructorRequiresDatabase(): void {
		$options = MysqlConfig::options();
		unset($options['database']);
		$this->expectException(Exception::class);
		new DB($options);
	}

	public function testConstructorRejectsUnknownEngine(): void {
		$options = MysqlConfig::options();
		$options['engine'] = 'nope';
		$this->expectException(Exception::class);
		new DB($options);
	}
}
