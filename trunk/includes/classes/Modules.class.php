<?php
/**
 * Modules
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Core
 */

if (defined('IN_ACP3') === false)
	exit;

/**
 * Klasse für die Module
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Core
 */
class ACP3_Modules
{
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
	public static function check($module = 0, $file = 0) {
		$module = !empty($module) ? $module : ACP3_CMS::$uri->mod;
		$file = !empty($file) ? $file : ACP3_CMS::$uri->file;

		if (is_file(MODULES_DIR . $module . '/' . $file . '.php') === true) {
			if (self::isActive($module) === true) {
				return ACP3_ACL::canAccessResource($module . '/' . $file . '/');
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
	public static function isActive($module)
	{
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
	public static function isInstalled($module)
	{
		return (bool) ACP3_CMS::$db->countRows('*', 'modules', 'name = \'' . ACP3_CMS::$db->escape($module) . '\'');
	}
	/**
	 * Gibt ein alphabetisch sortiertes Array mit allen gefundenen
	 * Modulen des ACP3 mitsamt Modulinformationen aus
	 *
	 * @return array
	 */
	public static function getAllModules($only_active = false)
	{
		static $mod_list = array();

		if (empty($mod_list)) {
			$dir = scandir(MODULES_DIR);
			foreach ($dir as $module) {
				$info = self::getModuleInfo($module);
				if (!empty($info) &&
					($only_active === false || ($only_active === true && self::isActive($module) === true)))
					$mod_list[$info['name']] = $info;
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
	public static function getActiveModules()
	{
		return self::getAllModules(true);
	}
	/**
	 * Durchläuft für das angeforderte Modul den <info> Abschnitt in der
	 * module.xml und gibt die gefundenen Informationen als Array zurück
	 *
	 * @param string $module
	 * @return array
	 */
	public static function getModuleInfo($module)
	{
		static $parsed_modules = array();

		if (empty($parsed_modules)) {
			$filename = 'modules_infos_' . ACP3_CMS::$lang->getLang();
			if (ACP3_Cache::check($filename) === false)
				self::setModulesCache();
			$parsed_modules = ACP3_Cache::output($filename);
		}
		return !empty($parsed_modules[$module]) ? $parsed_modules[$module] : array();
	}
	/**
	 * Setzt den Cache für alle vorliegenden Modulinformationen
	 */
	public static function setModulesCache()
	{
		$infos = array();
		$dirs = scandir(MODULES_DIR);
		foreach ($dirs as $dir) {
			if ($dir !== '.' && $dir !== '..' && is_file(MODULES_DIR . '/' . $dir . '/module.xml') === true) {
				$mod_info = ACP3_XML::parseXmlFile(MODULES_DIR . $dir . '/module.xml', 'info');

				if (is_array($mod_info) === true) {
					$mod_db = ACP3_CMS::$db->select('version, active', 'modules', 'name = \'' . ACP3_CMS::$db->escape($dir, 2) . '\'');
					$infos[$dir] = array(
						'dir' => $dir,
						'active' =>  isset($mod_db[0]) && $mod_db[0]['active'] == 1 ? true : false,
						'schema_version' => isset($mod_db[0]) ? (int) $mod_db[0]['version'] : 0,
						'description' => isset($mod_info['description']['lang']) && $mod_info['description']['lang'] === 'true' ? ACP3_CMS::$lang->t($dir, 'mod_description') : $mod_info['description']['lang'],
						'author' => $mod_info['author'],
						'version' => isset($mod_info['version']['core']) && $mod_info['version']['core'] === 'true' ? CONFIG_VERSION : $mod_info['version'],
						'name' => isset($mod_info['name']['lang']) && $mod_info['name']['lang'] == 'true' ? ACP3_CMS::$lang->t($dir, $dir) : $mod_info['name'],
						'categories' => isset($mod_info['categories']) ? true : false,
						'protected' => isset($mod_info['protected']) ? true : false,
					);
				}
			}
		}
		ACP3_Cache::create('modules_infos_' . ACP3_CMS::$lang->getLang(), $infos);
	}
}