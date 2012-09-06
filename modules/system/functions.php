<?php
/**
 * System
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

/**
 * Überprüft die Modulabhängigkeiten beim Installieren eines Moduls
 *
 * @param string $module
 * @return array
 */
function checkInstallDependencies($module)
{
	$deps = ACP3_ModuleInstaller::getDependencies($module);
	$mods_to_enable = array();
	if (!empty($deps)) {
		foreach ($deps as $dep) {
			if (ACP3_Modules::isActive($dep) === false) {
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
function checkUninstallDependencies($module)
{
	$modules = scandir(MODULES_DIR);
	$mods_to_uninstall = array();
	foreach ($modules as $row) {
		if ($row !== '.' && $row !== '..' && $row !== $module) {
			$deps = ACP3_ModuleInstaller::getDependencies($row);
			if (!empty($deps) &&
				ACP3_Modules::isInstalled($row) === true &&
				in_array(ACP3_CMS::$uri->dir, $deps) === true) {
				$mods_to_uninstall[] = ucfirst($row);
			}
		}
	}
	return $mods_to_uninstall;
}