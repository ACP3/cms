<?php

class ACP3_ArticlesModuleInstaller extends ACP3_ModuleInstaller {
	private $module_name = 'articles';
	private $schema_version = 32;

	public function renameModule() {
		return array(
			31 => "UPDATE `{pre}modules` SET name = 'articles' WHERE name = 'static_pages';"
		);
	}

	protected function getName() {
		return $this->module_name;
	}

	protected function getSchemaVersion() {
		return $this->schema_version;
	}

	protected function createTables() {
		return array(
			"CREATE TABLE `{pre}articles` (
				`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`start` DATETIME NOT NULL,
				`end` DATETIME NOT NULL,
				`title` VARCHAR(120) NOT NULL,
				`text` TEXT NOT NULL,
				`user_id` INT(10) UNSIGNED NOT NULL,
				PRIMARY KEY (`id`), FULLTEXT KEY `index` (`title`, `text`)
			) {engine} {charset};"
		);
	}

	protected function removeTables() {
		return array("DROP TABLE `{pre}articles`;");
	}

	protected function settings() {
		return array();
	}

	protected function schemaUpdates() {
		return array(
			31 => array(
				"RENAME TABLE `{pre}static_pages` TO `{pre}articles`;",
				"UPDATE `{pre}seo` SET uri = REPLACE(uri, 'static_pages', 'articles') WHERE uri REGEXP '^(static_pages/list/id_[0-9]+/)$';",
				"UPDATE `{pre}articles` SET `text` = REPLACE(`text`, 'static_pages/list/id_', 'articles/list/id_') WHERE `text` REGEXP '(static_pages/list/id_[0-9]+/)';",
				ACP3_Modules::isInstalled('menus') || ACP3_Modules::isInstalled('menu_items') ? "UPDATE `{pre}menu_items` SET uri = REPLACE(uri, 'static_pages', 'articles') WHERE uri REGEXP '^(static_pages/list/id_[0-9]+/)$';" : ''
			),
			32 => array(
				"INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `page`, `params`, `privilege_id`) VALUES('', " . $this->getModuleId() . ", 'details', '', 1);",
				"UPDATE `{pre}seo` SET uri = REPLACE(uri, '/list/', '/details/') WHERE uri REGEXP '^(articles/list/id_[0-9]+/)$';",
				"UPDATE `{pre}articles` SET `text` = REPLACE(`text`, 'articles/list/id_', 'articles/details/id_') WHERE `text` REGEXP '(articles/list/id_[0-9]+/)';",
				ACP3_Modules::isInstalled('menus') || ACP3_Modules::isInstalled('menu_items') ? "UPDATE `{pre}menu_items` SET uri = REPLACE(uri, '/list/', '/details/') WHERE uri REGEXP '^(articles/list/id_[0-9]+/)$';" : ''
			)
		);
	}
}