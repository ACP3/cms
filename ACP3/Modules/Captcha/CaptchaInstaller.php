<?php
namespace ACP3\Modules\Captcha;
use ACP3\Core\ModuleInstaller;

class CaptchaInstaller extends ModuleInstaller {
	const MODULE_NAME = 'captcha';
	const SCHEMA_VERSION = 31;

	protected function createTables() {
		return array();
	}

	protected function removeTables() {
		return array();
	}

	protected function settings() {
		return array();
	}

	protected function schemaUpdates() {
		return array(
			31 => array(
				"DELETE FROM `{pre}acl_resources` WHERE `module_id` = " . $this->getModuleId() . " AND page = \"functions\";",
			) 
		);
	}
}