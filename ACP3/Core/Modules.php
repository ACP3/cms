<?php

/**
 * Modules
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Core
 */

namespace ACP3\Core;

/**
 * Klasse für die Module
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Core
 */
abstract class Modules {

	/**
	 * Überpüft, ob ein Modul überhaupt existiert,
	 * bzw. der Benutzer auf ein Modul Zugriff hat
	 *
	 * @param string $module
	 * 	Zu überprüfendes Modul
	 * @param string $file
	 * 	Zu überprüfende Moduldatei
	 *
	 * @return integer
	 */
	public static function check($module, $file) {
		$module = strtolower($module);
		$moduleUc = ucfirst($module);
		$section = strpos($file, 'acp_') === 0 ? 'Admin' : 'Frontend';

		$path = MODULES_DIR . $moduleUc . '/' . $moduleUc . $section . '.php';

		if (is_file($path) === true) {
			$className = "\\ACP3\\Modules\\" . $moduleUc . "\\" . $moduleUc . $section;
			$method = 'action' . ucfirst($section === 'Admin' ? substr($file, 4) : $file);
			if (method_exists($className, $method) === true) {
				if (self::isActive($module) === true) {
					return ACL::canAccessResource($module . '/' . $file . '/');
				}
			}
			return 0;
		}
		return -1;
	}

	/**
	 * Gibt zurück, ob ein Modul aktiv ist oder nicht
	 *
	 * @param string $module
	 * @return boolean
	 */
	public static function isActive($module) {
		$info = self::getModuleInfo($module);
		return !empty($info) && $info['active'] == 1 ? true : false;
	}

	/**
	 * Überprüft, ob ein Modul in der modules DB-Tabelle
	 * eingetragen und somit installiert ist
	 *
	 * @param string $module
	 * @return boolean
	 */
	public static function isInstalled($module) {
		return Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'modules WHERE name = ?', array($module)) == 1 ? true : false;
	}

	/**
	 * Gibt ein alphabetisch sortiertes Array mit allen gefundenen
	 * Modulen des ACP3 mitsamt Modulinformationen aus
	 *
	 * @return array
	 */
	public static function getAllModules($only_active = false) {
		static $mod_list = array();

		if (empty($mod_list)) {
			$dir = scandir(MODULES_DIR);
			foreach ($dir as $module) {
				if ($module !== '.' && $module !== '..') {
					$info = self::getModuleInfo($module);
					if (!empty($info) &&
							($only_active === false || ($only_active === true && self::isActive($module) === true)))
						$mod_list[$info['name']] = $info;
				}
			}
			ksort($mod_list);
		}
		return $mod_list;
	}

	/**
	 * Gibt alle derzeit aktiven Module in einem Array zurück
	 *
	 * @return array
	 */
	public static function getActiveModules() {
		return self::getAllModules(true);
	}

	/**
	 * Durchläuft für das angeforderte Modul den <info> Abschnitt in der
	 * module.xml und gibt die gefundenen Informationen als Array zurück
	 *
	 * @param string $module
	 * @return array
	 */
	public static function getModuleInfo($module) {
		static $parsed_modules = array();

		$module = strtolower($module);
		if (empty($parsed_modules)) {
			$filename = 'infos_' . Registry::get('Lang')->getLanguage();
			if (Cache::check($filename, 'modules') === false)
				self::setModulesCache();
			$parsed_modules = Cache::output($filename, 'modules');
		}
		return !empty($parsed_modules[$module]) ? $parsed_modules[$module] : array();
	}

	/**
	 * Setzt den Cache für alle vorliegenden Modulinformationen
	 */
	public static function setModulesCache() {
		$infos = array();
		$dirs = scandir(MODULES_DIR);
		foreach ($dirs as $dir) {
			$path = MODULES_DIR . '/' . $dir . '/module.xml';
			if ($dir !== '.' && $dir !== '..' && is_file($path) === true) {
				$mod_info = XML::parseXmlFile($path, 'info');

				if (!empty($mod_info)) {
					$mod_name = strtolower($dir);
					$mod_db = Registry::get('Db')->fetchAssoc('SELECT version, active FROM ' . DB_PRE . 'modules WHERE name = ?', array($mod_name));
					$infos[$mod_name] = array(
						'dir' => $dir,
						'active' => !empty($mod_db) && $mod_db['active'] == 1 ? true : false,
						'schema_version' => !empty($mod_db) ? (int) $mod_db['version'] : 0,
						'description' => isset($mod_info['description']['lang']) && $mod_info['description']['lang'] === 'true' ? Registry::get('Lang')->t($mod_name, 'mod_description') : $mod_info['description']['lang'],
						'author' => $mod_info['author'],
						'version' => isset($mod_info['version']['core']) && $mod_info['version']['core'] === 'true' ? CONFIG_VERSION : $mod_info['version'],
						'name' => isset($mod_info['name']['lang']) && $mod_info['name']['lang'] == 'true' ? Registry::get('Lang')->t($mod_name, $mod_name) : $mod_info['name'],
						'categories' => isset($mod_info['categories']) ? true : false,
						'protected' => isset($mod_info['protected']) ? true : false,
					);
					$infos[$mod_name]['dependencies'] = array_values(XML::parseXmlFile($path, 'info/dependencies'));
				}
			}
		}
		Cache::create('infos_' . Registry::get('Lang')->getLanguage(), $infos, 'modules');
	}

}