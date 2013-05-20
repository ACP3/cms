<?php

namespace ACP3\Modules\Gallery;

use ACP3\Core\ModuleInstaller;

class GalleryInstaller extends ModuleInstaller {

	private $module_name = 'gallery';
	private $schema_version = 32;

	protected function getName() {
		return $this->module_name;
	}

	protected function getSchemaVersion() {
		return $this->schema_version;
	}

	protected function createTables() {
		return array(
			"CREATE TABLE `{pre}gallery` (
				`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`start` DATETIME NOT NULL,
				`end` DATETIME NOT NULL,
				`title` VARCHAR(120) NOT NULL,
				`user_id` INT UNSIGNED NOT NULL,
				PRIMARY KEY (`id`)
			) {engine} {charset};",
			"CREATE TABLE `{pre}gallery_pictures` (
				`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`pic` INT(10) UNSIGNED NOT NULL,
				`gallery_id` INT(10) UNSIGNED NOT NULL,
				`file` VARCHAR(120) NOT NULL,
				`description` TEXT NOT NULL,
				`comments` TINYINT(1) UNSIGNED NOT NULL,
				PRIMARY KEY (`id`), INDEX `foreign_gallery_id` (`gallery_id`)
			) {engine} {charset};"
		);
	}

	protected function removeTables() {
		return array(
			"DROP TABLE `{pre}gallery_pictures`;",
			"DROP TABLE `{pre}gallery`;"
		);
	}

	protected function settings() {
		return array(
			'width' => 640,
			'height' => 480,
			'thumbwidth' => 160,
			'thumbheight' => 120,
			'maxwidth' => 2048,
			'maxheight' => 1536,
			'filesize' => 20971520,
			'overlay' => 1,
			'comments' => 1,
			'dateformat' => 'long',
			'sidebar' => 5,
		);
	}

	protected function schemaUpdates() {
		return array(
			31 => array(
				"ALTER TABLE `{pre}gallery` CHANGE `name` `title` VARCHAR(120) {charset} NOT NULL;",
			),
			32 => array(
				"DELETE FROM `{pre}acl_resources` WHERE `module_id` = " . $this->getModuleId() . " AND page = \"functions\";",
			)
		);
	}

}