<?php

class ACP3_NewsModuleInstaller extends ACP3_ModuleInstaller {
	private $module_name = 'news';
	private $schema_version = 31;

	protected function getName() {
		return $this->module_name;
	}

	protected function getSchemaVersion() {
		return $this->schema_version;
	}

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
			)
		);
	}
}