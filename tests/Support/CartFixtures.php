<?php

declare(strict_types=1);

namespace Tests\Support;

use Opencart\System\Engine\Config;
use Opencart\System\Engine\Registry;
use Opencart\System\Library\DB;

final class CartFixtures {
	public static function registryWithDb(): Registry {
		$db = new DB(MysqlConfig::options());
		$registry = new Registry();
		$config = new Config();
		$config->set('config_language_id', 1);
		$config->set('config_customer_group_id', 1);
		$registry->set('config', $config);
		$registry->set('db', $db);

		return $registry;
	}

	public static function ensureLocalizationTables(DB $db): void {
		$p = DB_PREFIX;
		$db->query("CREATE TABLE IF NOT EXISTS `{$p}length_class` (
			`length_class_id` int(11) NOT NULL AUTO_INCREMENT,
			`value` decimal(15,8) NOT NULL,
			PRIMARY KEY (`length_class_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
		$db->query("CREATE TABLE IF NOT EXISTS `{$p}length_class_description` (
			`length_class_id` int(11) NOT NULL,
			`language_id` int(11) NOT NULL,
			`title` varchar(32) NOT NULL,
			`unit` varchar(4) NOT NULL,
			PRIMARY KEY (`length_class_id`,`language_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
		$db->query("CREATE TABLE IF NOT EXISTS `{$p}weight_class` (
			`weight_class_id` int(11) NOT NULL AUTO_INCREMENT,
			`value` decimal(15,8) NOT NULL,
			PRIMARY KEY (`weight_class_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
		$db->query("CREATE TABLE IF NOT EXISTS `{$p}weight_class_description` (
			`weight_class_id` int(11) NOT NULL,
			`language_id` int(11) NOT NULL,
			`title` varchar(32) NOT NULL,
			`unit` varchar(4) NOT NULL,
			PRIMARY KEY (`weight_class_id`,`language_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
		$db->query("CREATE TABLE IF NOT EXISTS `{$p}currency` (
			`currency_id` int(11) NOT NULL AUTO_INCREMENT,
			`title` varchar(32) NOT NULL,
			`code` varchar(3) NOT NULL,
			`symbol_left` varchar(12) NOT NULL,
			`symbol_right` varchar(12) NOT NULL,
			`decimal_place` int(1) NOT NULL DEFAULT 2,
			`value` double(15,8) NOT NULL,
			`status` tinyint(1) NOT NULL DEFAULT 1,
			`date_modified` datetime NOT NULL,
			PRIMARY KEY (`currency_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
		$db->query("CREATE TABLE IF NOT EXISTS `{$p}tax_rate` (
			`tax_rate_id` int(11) NOT NULL AUTO_INCREMENT,
			`geo_zone_id` int(11) NOT NULL DEFAULT 0,
			`name` varchar(32) NOT NULL,
			`rate` decimal(15,4) NOT NULL DEFAULT 0,
			`type` char(1) NOT NULL,
			PRIMARY KEY (`tax_rate_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
		$db->query("CREATE TABLE IF NOT EXISTS `{$p}tax_rule` (
			`tax_rule_id` int(11) NOT NULL AUTO_INCREMENT,
			`tax_class_id` int(11) NOT NULL,
			`tax_rate_id` int(11) NOT NULL,
			`based` varchar(10) NOT NULL,
			`priority` int(11) NOT NULL,
			PRIMARY KEY (`tax_rule_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
		$db->query("CREATE TABLE IF NOT EXISTS `{$p}tax_rate_to_customer_group` (
			`tax_rate_id` int(11) NOT NULL,
			`customer_group_id` int(11) NOT NULL,
			PRIMARY KEY (`tax_rate_id`,`customer_group_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
		$db->query("CREATE TABLE IF NOT EXISTS `{$p}zone_to_geo_zone` (
			`zone_to_geo_zone_id` int(11) NOT NULL AUTO_INCREMENT,
			`country_id` int(11) NOT NULL,
			`zone_id` int(11) NOT NULL,
			`geo_zone_id` int(11) NOT NULL,
			PRIMARY KEY (`zone_to_geo_zone_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
		$db->query("CREATE TABLE IF NOT EXISTS `{$p}geo_zone` (
			`geo_zone_id` int(11) NOT NULL AUTO_INCREMENT,
			`name` varchar(32) NOT NULL,
			`description` varchar(255) NOT NULL,
			PRIMARY KEY (`geo_zone_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

		$db->query("DELETE FROM `{$p}length_class`");
		$db->query("DELETE FROM `{$p}length_class_description`");
		$db->query("INSERT INTO `{$p}length_class` (`length_class_id`,`value`) VALUES (1,1),(2,10)");
		$db->query("INSERT INTO `{$p}length_class_description` VALUES (1,1,'Centimeter','cm'),(2,1,'Millimeter','mm')");

		$db->query("DELETE FROM `{$p}weight_class`");
		$db->query("DELETE FROM `{$p}weight_class_description`");
		$db->query("INSERT INTO `{$p}weight_class` (`weight_class_id`,`value`) VALUES (1,1),(2,1000),(3,2.2046)");
		$db->query("INSERT INTO `{$p}weight_class_description` VALUES (1,1,'Kilogram','kg'),(2,1,'Gram','g'),(3,1,'Pound','lb')");

		$db->query("DELETE FROM `{$p}currency`");
		$db->query("INSERT INTO `{$p}currency` (`currency_id`,`title`,`code`,`symbol_left`,`symbol_right`,`decimal_place`,`value`,`status`,`date_modified`) VALUES
			(1,'US Dollar','USD','\$','',2,1.00000000,1,NOW()),
			(2,'Euro','EUR','','€',2,0.80000000,1,NOW())");

		$db->query("DELETE FROM `{$p}tax_rate`");
		$db->query("DELETE FROM `{$p}tax_rule`");
		$db->query("DELETE FROM `{$p}tax_rate_to_customer_group`");
		$db->query("DELETE FROM `{$p}zone_to_geo_zone`");
		$db->query("DELETE FROM `{$p}geo_zone`");
		$db->query("INSERT INTO `{$p}geo_zone` (`geo_zone_id`,`name`,`description`) VALUES (3,'UK','UK')");
		$db->query("INSERT INTO `{$p}zone_to_geo_zone` (`country_id`,`zone_id`,`geo_zone_id`) VALUES (1,2,3)");
		$db->query("INSERT INTO `{$p}tax_rate` (`tax_rate_id`,`geo_zone_id`,`name`,`rate`,`type`) VALUES (86,3,'VAT',20.0000,'P'),(87,3,'Eco',2.0000,'F')");
		$db->query("INSERT INTO `{$p}tax_rate_to_customer_group` VALUES (86,1),(87,1)");
		$db->query("INSERT INTO `{$p}tax_rule` (`tax_rule_id`,`tax_class_id`,`tax_rate_id`,`based`,`priority`) VALUES (128,9,86,'shipping',1),(127,9,87,'shipping',2)");
	}
}
