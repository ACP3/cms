<?php
/**
 * Modules
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */
/**
 * Klasse für die Module
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */
class modules
{
	/**
	 * Überpüft, ob ein Modul überhaupt existiert, bzw. der Benutzer auf ein Modul Zugriff hat
	 *
	 * @param string $module
	 * 	Zu überprüfendes Modul
	 * @param string $page
	 * 	Zu überprüfende Moduldatei
	 *
	 * @return boolean
	 */
	public static function check($module = 0, $page = 0) {
		global $auth, $db, $uri;
		static $access_level = array();

		$module = !empty($module) ? $module : $uri->mod;
		$page = !empty($page) ? $page : $uri->page;

		if (is_file(ACP3_ROOT . 'modules/' . $module . '/' . $page . '.php')) {
			$xml = simplexml_load_file(ACP3_ROOT . 'modules/' . $module . '/module.xml');

			if ((string) $xml->info->active == '1') {
				// Falls die einzelnen Zugriffslevel auf die Module noch nicht gesetzt sind, diese aus der Datenbank selektieren
				if (!isset($access_level[$module])) {
					// Zugriffslevel für Gäste
					$access_id = 2;
					// Zugriffslevel des Benutzers holen
					if ($auth->isUser()) {
						$info = $auth->getUserInfo();
						if (!empty($info)) {
							$access_id = $info['access'];
						}
					}
					$access_to_modules = $db->select('modules', 'access', 'id = \'' . $access_id . '\'');
					$modules = explode(',', $access_to_modules[0]['modules']);

					foreach ($modules as $row) {
						$access_level[substr($row, 0, -2)] = substr($row, -1, 1);
					}
				}

				// XML Datei parsen
				foreach ($xml->access->item as $item) {
					if ((string) $item->file == $page && (string) $item->level != '0' && isset($access_level[$module]) && (string) $item->level <= $access_level[$module]) {
						// Zusätzliche include-Files einbinden
						if (!empty($item->include)) {
							$includes = explode(',', $item->include);
							foreach ($includes as $file) {
								$path = ACP3_ROOT . 'modules/' . (!preg_match('=/=', $file) ? $module . '/' . $file : $file) . '.php';
								if (is_file($path)) {
									require_once $path;
								}
							}
						}
						// User hat Zugriff auf die aktuelle Seite
						return true;
					}
				}
			}
		}
		return false;
	}
	/**
	 * Gibt ein alphabetisch sortiertes Array mit allen gefundenen Modulen des ACP3 mitsamt Modulinformationen aus
	 *
	 * @return array
	 */
	public static function modulesList()
	{
		static $mod_list = array();

		if (empty($mod_list)) {
			$uri_dir = scandir(ACP3_ROOT . 'modules/');
			foreach ($uri_dir as $module) {
				$info = self::parseInfo($module);
				if (!empty($info)) {
					$mod_list[$info['name']] = $info;
				}
			}
			ksort($mod_list);
		}
		return $mod_list;
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
		global $lang;
		static $parsed_modules = array();

		if (empty($parsed_modules[$module])) {
			$mod_info = xml::parseXmlFile(ACP3_ROOT . 'modules/' . $module . '/module.xml', 'info');

			if (is_array($mod_info)) {
				$parsed_modules[$module] = array(
					'dir' => $module,
					'active' => $mod_info['active'],
					'description' => isset($mod_info['description']['lang']) && $mod_info['description']['lang'] == 'true' ? $lang->t($module, 'mod_description') : $mod_info['description']['lang'],
					'name' => isset($mod_info['name']['lang']) && $mod_info['name']['lang'] == 'true' ? $lang->t($module, $module) : $mod_info['name'],
					'tables' => !empty($mod_info['tables']) ? explode(',', $mod_info['tables']) : false,
					'categories' => isset($mod_info['categories']) ? true : false,
					'protected' => isset($mod_info['protected']) ? true : false,
				);
				return $parsed_modules[$module];
			}
			return array();
		}
		return $parsed_modules[$module];
	}
}
?>