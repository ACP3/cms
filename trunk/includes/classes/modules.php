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
	private static $minify = array();

	/**
	 * Überpüft, ob ein Modul überhaupt existiert,
	 * bzw. der Benutzer auf ein Modul Zugriff hat
	 *
	 * @param string $module
	 * 	Zu überprüfendes Modul
	 * @param string $page
	 * 	Zu überprüfende Moduldatei
	 *
	 * @return integer
	 */
	public static function check($module = 0, $page = 0) {
		global $auth, $db, $uri;
		static $access_level = array();

		$module = !empty($module) ? $module : $uri->mod;
		$page = !empty($page) ? $page : $uri->page;

		if (is_file(ACP3_ROOT . 'modules/' . $module . '/' . $page . '.php')) {
			$xml = simplexml_load_file(ACP3_ROOT . 'modules/' . $module . '/module.xml');

			if ((string) $xml->info->active == '1') {
				// Falls die einzelnen Zugriffslevel auf die Module noch nicht
				// gesetzt sind, diese aus der Datenbank selektieren
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
						$pos = strrpos($row, ':');
						$access_level[substr($row, 0, $pos)] = (int) substr($row, $pos + 1);
					}
				}

				// XML Datei parsen
				foreach ($xml->access->item as $item) {
					if ((string) $item->file == $page) {
						if ((int) $item->level != 0) {
							$levels = array(
								1 => array(1),
								2 => array(2),
								3 => array(3, 2, 1),
								4 => array(4),
								5 => array(5, 4, 1),
								6 => array(6, 4, 2),
								7 => array(7, 4, 2, 1),
								8 => array(8),
								9 => array(9, 8, 1),
								10 => array(10, 8, 2),
								11 => array(11, 8, 2, 1),
								12 => array(12, 8, 4),
								13 => array(13, 8, 4, 1),
								14 => array(14, 8, 4, 2),
								15 => array(15, 8, 4, 2, 1),
							);
							if (!empty($access_level[$module]) && in_array((int) $item->level, $levels[$access_level[$module]]))
								return 1;
						}
						return 0;
					}
				}
			}
			return 0;
		}
		return -1;
	}
	public static function minify($mode)
	{
		$defaults = $mode == 'js' ? 'jquery.js,jquery.cookie.js,jquery.ui.js,script.js' : 'style.css,jquery-ui.css';
		return ROOT_DIR . 'includes/min/?b=' . substr(DESIGN_PATH, 1, -1) . '&amp;f=' . $defaults . (!empty(self::$minify[$mode]) ? implode(',', $minify[$mode]) : '');
	}
	/**
	 * Gibt ein alphabetisch sortiertes Array mit allen gefundenen
	 * Modulen des ACP3 mitsamt Modulinformationen aus
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
	 * Gibt die Seite aus
	 */
	public static function outputPage() {
		global $auth, $date, $db, $lang, $tpl, $uri;

		if (!$auth->isUser() && defined('IN_ADM') && uri($uri->query) != uri('acp/users/login')) {
			redirect('acp/users/login');
		}

		switch (modules::check()) {
			// Seite ausgeben
			case 1:
				require ACP3_ROOT . 'modules/' . $uri->mod . '/' . $uri->page . '.php';

				// Evtl. gesetzten Content-Type des Servers überschreiben
				header('Content-Type: ' . (defined('CUSTOM_CONTENT_TYPE') ? CUSTOM_CONTENT_TYPE : 'text/html') . '; charset=UTF-8');

				$tpl->assign('TITLE', breadcrumb::output(2));
				$tpl->assign('BREADCRUMB', breadcrumb::output());
				$tpl->assign('CONTENT', !empty($content) ? $content : '');

				// Falls ein Modul ein eigenes Layout verwenden möchte, dieses auch zulassen
				$output = $tpl->fetch(defined('CUSTOM_LAYOUT') ? CUSTOM_LAYOUT : 'layout.html');
				echo str_replace(array('<!-- STYLESHEET -->', '<!-- JAVASCRIPT -->'), array(modules::minify('css'), modules::minify('js')), $output);
				break;
			// Kein Zugriff auf die Seite
			case 0:
				redirect('errors/403');
				break;
			// Seite nicht gefunden
			default:
				redirect('errors/404');
		}
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
					'author' => $mod_info['author'],
					'version' => isset($mod_info['version']['core']) && $mod_info['version']['core'] == 'true' ? CONFIG_VERSION : $mod_info['version'],
					'name' => isset($mod_info['name']['lang']) && $mod_info['name']['lang'] == 'true' ? $lang->t($module, $module) : $mod_info['name'],
					'tables' => !empty($mod_info['tables']) ? explode(',', $mod_info['tables']) : false,
					'categories' => isset($mod_info['categories']) ? true : false,
					'javascript' => isset($mod_info['javascript']) ? true : false,
					'stylesheet' => isset($mod_info['stylesheet']) ? true : false,
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