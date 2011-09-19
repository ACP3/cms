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
								1 => array(1), // Lesen
								2 => array(2), // Erstellen
								3 => array(3, 2, 1), // Lesen und Erstellen
								4 => array(4), // Bearbeiten
								5 => array(5, 4, 1), // Bearbeiten und Lesen
								6 => array(6, 4, 2), // Bearbeiten und Erstellen
								7 => array(7, 4, 2, 1), // Bearbeiten, Erstellen und Lesen
								8 => array(8), // Löschen
								9 => array(9, 8, 1), // Löschen und Lesen
								10 => array(10, 8, 2), // Löschen und Erstellen
								11 => array(11, 8, 2, 1), // Löschen, Erstellen und Lesen
								12 => array(12, 8, 4), // Löschen und Bearbeiten
								13 => array(13, 8, 4, 1), // Löschen, Bearbeiten und Lesen
								14 => array(14, 8, 4, 2), // Löschen, Bearbeiten und Erstellen
								15 => array(15, 8, 4, 2, 1), // Löschen, Bearbeiten, Erstellen und Lesen
								16 => array(16, 8, 4, 2, 1), // Vollzugriff
							);
							if (!empty($access_level[$module]) && !empty($levels[$access_level[$module]]) &&
								in_array((int) $item->level, $levels[$access_level[$module]]))
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
		global $auth, $uri;

		if (!$auth->isUser() && defined('IN_ADM') && $uri->mod != 'users' && $uri->page != 'login') {
			$redirect_uri = base64_encode(substr(str_replace(PHP_SELF, '', htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES)), 1));
			redirect('acp/users/login/redirect_' . $redirect_uri);
		}

		switch (modules::check()) {
			// Seite ausgeben
			case 1:
				global $date, $db, $lang, $tpl;

				require ACP3_ROOT . 'modules/' . $uri->mod . '/' . $uri->page . '.php';

				// Evtl. gesetzten Content-Type des Servers überschreiben
				header('Content-Type: ' . (defined('CUSTOM_CONTENT_TYPE') ? CUSTOM_CONTENT_TYPE : 'text/html') . '; charset=UTF-8');

				$tpl->assign('TITLE', breadcrumb::output(2));
				$tpl->assign('BREADCRUMB', breadcrumb::output());
				$tpl->assign('KEYWORDS', seo::getCurrentKeywordsOrDescription());
				$tpl->assign('DESCRIPTION', seo::getCurrentKeywordsOrDescription(2));
				$tpl->assign('CONTENT', !empty($content) ? $content : '');

				// Falls ein Modul ein eigenes Layout verwenden möchte, dieses auch zulassen
				self::fetchTemplate(defined('CUSTOM_LAYOUT') ? CUSTOM_LAYOUT : 'layout.html', null, null, null, true);
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
	 * Gibt ein Template aus
	 *
	 * @param string $template
	 * @param mixed $cache_id
	 * @param mixed $compile_id
	 * @param object $parent
	 * @param boolean $display
	 * @return string
	 */
	public static function fetchTemplate($template, $cache_id = null, $compile_id = null, $parent = null, $display = false)
	{
		global $lang, $tpl;

		if ($tpl->templateExists($template)) {
			return $tpl->fetch($template, $cache_id, $compile_id, $parent, $display);
		} elseif (defined('DEBUG') && DEBUG) {
			return sprintf($lang->t('errors', 'tpl_not_found'), $template);
		}

		return '';
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

		if (empty($parsed_modules[$module]) && !preg_match('=/=', $module)) {
			$mod_info = xml::parseXmlFile(ACP3_ROOT . 'modules/' . $module . '/module.xml', 'info');

			if (is_array($mod_info)) {
				global $lang;

				$parsed_modules[$module] = array(
					'dir' => $module,
					'active' => $mod_info['active'],
					'description' => isset($mod_info['description']['lang']) && $mod_info['description']['lang'] == 'true' ? $lang->t($module, 'mod_description') : $mod_info['description']['lang'],
					'author' => $mod_info['author'],
					'version' => isset($mod_info['version']['core']) && $mod_info['version']['core'] == 'true' ? CONFIG_VERSION : $mod_info['version'],
					'name' => isset($mod_info['name']['lang']) && $mod_info['name']['lang'] == 'true' ? $lang->t($module, $module) : $mod_info['name'],
					'tables' => !empty($mod_info['tables']) ? explode(',', $mod_info['tables']) : false,
					'categories' => isset($mod_info['categories']) ? true : false,
					'js' => isset($mod_info['js']) ? true : false,
					'css' => isset($mod_info['css']) ? true : false,
					'protected' => isset($mod_info['protected']) ? true : false,
				);
				return $parsed_modules[$module];
			}
			return array();
		}
		return $parsed_modules[$module];
	}
}