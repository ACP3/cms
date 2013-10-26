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

abstract class Helpers {

	/**
	 * Überprüft die Modulabhängigkeiten beim Installieren eines Moduls
	 *
	 * @param string $module
	 * @return array
	 */
	public static function checkInstallDependencies($module)
	{
		$module = strtolower($module);
		$deps = Core\Modules\Installer::getDependencies($module);
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
		$module = strtolower($module);
		$modules = scandir(MODULES_DIR);
		$mods_to_uninstall = array();
		foreach ($modules as $row) {
			$row = strtolower($row);
			if ($row !== '.' && $row !== '..' && $row !== $module) {
				$deps = Core\Modules\Installer::getDependencies($row); // Modulabhängigkeiten
				if (!empty($deps) && Core\Modules::isInstalled($row) === true && in_array($module, $deps) === true) {
					$mods_to_uninstall[] = ucfirst($row);
				}
			}
		}
		return $mods_to_uninstall;
	}

}