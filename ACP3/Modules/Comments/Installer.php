<?php

namespace ACP3\Modules\Comments;

use ACP3\Core\Modules;

class Installer extends Modules\Installer {

	const MODULE_NAME = 'comments';
	const SCHEMA_VERSION = 32;

	protected function createTables() {
		return array(
			"CREATE TABLE `{pre}comments` (
				`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`ip` VARCHAR(40) NOT NULL,
				`date` DATETIME NOT NULL,
				`name` VARCHAR(20) NOT NULL,
				`user_id` INT(10) UNSIGNED NOT NULL,
				`message` TEXT NOT NULL,
				`module_id` INT(10) UNSIGNED NOT NULL,
				`entry_id` INT(10) UNSIGNED NOT NULL,
				PRIMARY KEY (`id`), INDEX (`module_id`, `entry_id`)
			) {engine} {charset};"
		);
	}

	protected function removeTables() {
		return array("DROP TABLE `{pre}comments`;");
	}

	protected function settings() {
		return array(
			'dateformat' => 'long',
			'emoticons' => 1,
		);
	}

	protected function schemaUpdates() {
		return array(
			31 => array(
				"INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `page`, `params`, `privilege_id`) VALUES ('', " . $this->getModuleId() . ", 'acp_list_comments', '', 3);",
				"UPDATE `{pre}acl_resources` SET page = 'acp_delete' WHERE module_id = " . $this->getModuleId() . " AND page = 'acp_delete_comments_per_module';",
			),
			32 => array(
				"DELETE FROM `{pre}acl_resources` WHERE `module_id` = " . $this->getModuleId() . " AND page = \"functions\";",
				"INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `page`, `params`, `privilege_id`) VALUES('', " . $this->getModuleId() . ", 'list', '', 1);",
			)
		);
	}

}