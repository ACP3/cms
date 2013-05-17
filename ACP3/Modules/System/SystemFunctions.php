<?php

/**
 * System
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

namespace ACP3\Modules\System;

use ACP3\Core;

class SystemFunctions {

	/**
	 * Überprüft die Modulabhängigkeiten beim Installieren eines Moduls
	 *
	 * @param string $module
	 * @return array
	 */
	public static function checkInstallDependencies($module)
	{
		$deps = Core\ModuleInstaller::getDependencies($module);
		$mods_to_enable = array();
		if (!empty($deps)) {
			foreach ($deps as $dep) {
				if (Core\Modules::isActive($dep) === false) {
					$mods_to_enable[] = ucfirst($dep);
				}
			}
		}
		return $mods_to_enable;
	}

	/**
	 * Überprüft die Modulabhängigkeiten vor dem Deinstallieren eines Moduls
	 *
	 * @param string $module
	 * @return array
	 */
	public static function checkUninstallDependencies($module)
	{
		$modules = scandir(MODULES_DIR);
		$mods_to_uninstall = array();
		foreach ($modules as $row) {
			if ($row !== '.' && $row !== '..' && $row !== $module) {
				$deps = Core\ModuleInstaller::getDependencies($row);
				if (!empty($deps) &&
						Core\Modules::isInstalled($row) === true &&
						in_array(Core\Registry::get('URI')->dir, $deps) === true) {
					$mods_to_uninstall[] = ucfirst($row);
				}
			}
		}
		return $mods_to_uninstall;
	}

}