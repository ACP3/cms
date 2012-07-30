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
		global $uri;

		$module = !empty($module) ? $module : $uri->mod;
		$file = !empty($file) ? $file : $uri->file;

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
		$info = self::parseInfo($module);
		return $info['active'] == 1 ? true : false;
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
			$uri_dir = scandir(MODULES_DIR);
			foreach ($uri_dir as $module) {
				$info = self::parseInfo($module);
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
	public static function parseInfo($module)
	{
		static $parsed_modules = array();

		if (empty($parsed_modules)) {
			global $auth;

			$filename = 'modules_infos_' . $auth->getUserLanguage();
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
					global $auth, $db, $lang;

					$infos[$dir] = array(
						'dir' => $dir,
						'active' => $db->countRows('*', 'modules', 'name = \'' . $db->escape($dir, 2) . '\' AND active = 1') == 1 ? true : false,
						'description' => isset($mod_info['description']['lang']) && $mod_info['description']['lang'] == 'true' ? $lang->t($dir, 'mod_description') : $mod_info['description']['lang'],
						'author' => $mod_info['author'],
						'version' => isset($mod_info['version']['core']) && $mod_info['version']['core'] == 'true' ? CONFIG_VERSION : $mod_info['version'],
						'name' => isset($mod_info['name']['lang']) && $mod_info['name']['lang'] == 'true' ? $lang->t($dir, $dir) : $mod_info['name'],
						'categories' => isset($mod_info['categories']) ? true : false,
						'js' => isset($mod_info['js']) ? true : false,
						'css' => isset($mod_info['css']) ? true : false,
						'protected' => isset($mod_info['protected']) ? true : false,
					);
				}
			}
		}
		ACP3_Cache::create('modules_infos_' . $auth->getUserLanguage(), $infos);
	}
}