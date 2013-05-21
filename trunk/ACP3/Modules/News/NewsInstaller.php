<?php

namespace ACP3\Modules\News;

use ACP3\Core\ModuleInstaller;

class NewsInstaller extends ModuleInstaller {

	const MODULE_NAME = 'news';
	const SCHEMA_VERSION = 32;

	protected function createTables() {
		return array(
			"CREATE TABLE `{pre}news` (
				`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`start` DATETIME NOT NULL,
				`end` DATETIME NOT NULL,
				`title` VARCHAR(120) NOT NULL,
				`text` TEXT NOT NULL,
				`readmore` TINYINT(1) UNSIGNED NOT NULL,
				`comments` TINYINT(1) UNSIGNED NOT NULL,
				`category_id` INT(10) UNSIGNED NOT NULL,
				`uri` VARCHAR(120) NOT NULL,
				`target` TINYINT(1) UNSIGNED NOT NULL,
				`link_title` VARCHAR(120) NOT NULL,
				`user_id` INT UNSIGNED NOT NULL,
				PRIMARY KEY (`id`), FULLTEXT KEY `index` (`title`,`text`), INDEX `foreign_category_id` (`category_id`)
			) {engine} {charset};"
		);
	}

	protected function removeTables() {
		return array(
			"DROP TABLE `{pre}news`;",
			"DELETE FROM `{pre}categories` WHERE module_id = " . $this->getModuleId() . ";"
		);
	}

	protected function settings() {
		return array(
			'comments' => 1,
			'dateformat' => 'long',
			'readmore' => 1,
			'readmore_chars' => 350,
			'sidebar' => 5,
			'category_in_breadcrumb' => 1
		);
	}

	protected function schemaUpdates() {
		return array(
			31 => array(
				"ALTER TABLE `{pre}news` CHANGE `headline` `title` VARCHAR(120) {charset} NOT NULL",
			),
			32 => array(
				"DELETE FROM `{pre}acl_resources` WHERE `module_id` = " . $this->getModuleId() . " AND page = \"extensions/search\";",
				"DELETE FROM `{pre}acl_resources` WHERE `module_id` = " . $this->getModuleId() . " AND page = \"extensions/feeds\";",
				"DELETE FROM `{pre}acl_resources` WHERE `module_id` = " . $this->getModuleId() . " AND page = \"functions\";",
			)
		);
	}

}