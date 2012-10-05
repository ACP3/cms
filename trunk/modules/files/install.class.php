<?php

class ACP3_FilesModuleInstaller extends ACP3_ModuleInstaller {
	private $module_name = 'files';
	private $schema_version = 31;

	protected function getName() {
		return $this->module_name;
	}

	protected function getSchemaVersion() {
		return $this->schema_version;
	}

	protected function createTables() {
		return array(
			"CREATE TABLE `{pre}files` (
				`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`start` DATETIME NOT NULL,
				`end` DATETIME NOT NULL,
				`category_id` INT(10) UNSIGNED NOT NULL,
				`file` VARCHAR(120) NOT NULL,
				`size` VARCHAR(20) NOT NULL,
				`title` VARCHAR(120) NOT NULL,
				`text` TEXT NOT NULL,
				`comments` TINYINT(1) UNSIGNED NOT NULL,
				`user_id` INT UNSIGNED NOT NULL,
				PRIMARY KEY (`id`), FULLTEXT KEY `index` (`title`, `text`), INDEX `foreign_category_id` (`category_id`)
			) {engine} {charset};"
		);
	}

	protected function removeTables() {
		return array(
			"DROP TABLE `{pre}files`;",
			"DELETE FROM `{pre}categories` WHERE module_id = " . $this->getModuleId() . ";"
		);
	}

	protected function settings() {
		return array(
			'comments' => 1,
			'dateformat' => 'long',
			'sidebar' => 5,
		);
	}

	protected function schemaUpdates() {
		return array(
			31 => array(
				"ALTER TABLE `{pre}files` CHANGE `link_title` `title` VARCHAR(120) {charset} NOT NULL;",
			)
		);
	}
}