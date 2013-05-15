<?php
namespace ACP3\Modules\Categories;
use ACP3\Core\ModuleInstaller;

class CategoriesInstaller extends ModuleInstaller {
	private $module_name = 'categories';
	private $schema_version = 31;

	public function __construct(\ACP3\Core\Pimple $injector) {
		parent::__construct($injector);
	}

	protected function getName() {
		return $this->module_name;
	}

	protected function getSchemaVersion() {
		return $this->schema_version;
	}

	protected function createTables() {
		return array(
			"CREATE TABLE `{pre}categories` (
				`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`title` VARCHAR(120) NOT NULL,
				`picture` VARCHAR(120) NOT NULL,
				`description` VARCHAR(120) NOT NULL,
				`module_id` INT(10) UNSIGNED NOT NULL,
				PRIMARY KEY (`id`)
			) {engine} {charset};"
		);
	}

	protected function removeTables() {
		return array("DROP TABLE `{pre}categories`;");
	}

	protected function settings() {
		return array(
			'width' => 100,
			'height' => 50,
			'filesize' => 40960
		);
	}

	protected function schemaUpdates() {
		return array(
			31 => array(
				"ALTER TABLE `{pre}categories` CHANGE `name` `title` VARCHAR(120) {charset} NOT NULL;",
			)
		);
	}
}