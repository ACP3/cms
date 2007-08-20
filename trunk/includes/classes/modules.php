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
	public $id = '0';
	public $cat = '0';
	public $action = '';
	public $gen = array();

	/**
	 * Zerlegt u.a. der übergebenen Parameter in der URL in ihre Bestandteile
	 *
	 * @return modules
	 */
	function __construct()
	{
		if (!empty($_GET['stm']) && eregi('^(acp/)', $_GET['stm'])) {
			/**
			 * Definieren, dass man sich im Administrationsbereich befindet
			 */
			define('IN_ADM', true);
			// "acp/" entfernen
			$_GET['stm'] = substr($_GET['stm'], 4, strlen($_GET['stm']));
		} else {
			/**
			 * Definieren, dass man sich im Frontend befindet
			 */
			define('IN_ACP3', true);
		}
		$stm = !empty($_GET['stm']) ? explode('/', $_GET['stm']) : null;
		$c_stm = count($stm);
		$def_mod = defined('IN_ADM') ? 'home' : 'news';
		$def_page = defined('IN_ADM') ? 'adm_list' : 'list';

		$this->mod = !empty($stm[0]) ? $stm[0] : $def_mod;
		$this->page = !empty($stm[1]) ? $stm[1] : $def_page;

		// Modul und Seite aus Array entfernen und restlichen Einträge zählen
		unset($stm[0]);
		unset($stm[1]);

		$this->id = '0';
		$this->cat = !empty($_POST['cat']) ? $_POST['cat'] : '0';
		$this->action = !empty($_POST['action']) ? $_POST['action'] : $this->page;

		if ($c_stm > 0) {
			for ($i = 2; $i < $c_stm; $i++) {
				if (!empty($stm[$i])) {
					if (ereg('^(pos_[0-9]+)$', $stm[$i]) && !defined('POS'))
						define('POS', str_replace('pos_', '', $stm[$i]));
					if (ereg('^(id_[0-9]+)$', $stm[$i])) {
						$this->id = str_replace('id_', '', $stm[$i]);
					} elseif (ereg('^(cat_[0-9]+)$', $stm[$i])) {
						$this->cat = str_replace('cat_', '', $stm[$i]);
					} elseif (ereg('^(action_[_a-z0-9-]+)$', $stm[$i])) {
						$this->action = str_replace('action_', '', $stm[$i]);
					} elseif (ereg('^([_a-z0-9-]+)_(.+)$', $stm[$i])) {
						$pos = strpos($stm[$i], '_');
						$this->gen[substr($stm[$i], 0, $pos)] = substr($stm[$i], $pos + 1, strlen($stm[$i]));
					}
				}
			}
		}
		if (!defined('POS')) {
			define('POS', '0');
		}
	}
	/**
	 * Gibt ein alphabetisch sortiertes Array mit den zur Zeit aktivierten Modulen aus
	 *
	 * @return array
	 */
	function active_modules()
	{
		$modules = scandir('modules/');
		$active_modules = array();

		foreach ($modules as $module) {
			if ($this->is_active($module)) {
				$mod_info = array();
				include 'modules/' . $module . '/info.php';
				$active_modules[$mod_info['name']] = $module;
			}
		}
		ksort($active_modules);

		return $active_modules;
	}
	/**
	 * Überpüft, ob ein Modul überhaupt existiert, bzw. der Benutzer auf ein Modul Zugriff hat
	 *
	 * @param string $module
	 * 	Zu überprüfendes Modul
	 * @param string $page
	 * 	Zu überprüfende Moduldatei
	 * @return boolean
	 */
	function check($module = 0, $page = 0) {
		global $db;
		static $access_level = array();

		$module = !empty($module) ? $module : $this->mod;
		$page = !empty($page) ? $page : $this->page;

		if (isset($_SESSION) && is_file('modules/' . $module . '/' . $page . '.php')) {
			$xml = simplexml_load_file('modules/' . $module . '/access.xml');

			if ((string) $xml->active == '1') {
				if (!isset($access_level[$module])) {
					$access_to_modules = $db->select('modules', 'access', 'id = \'' . $_SESSION['acp3_access'] . '\'');
					$modules = explode(',', $access_to_modules[0]['modules']);

					foreach ($modules as $row) {
						$access_level[substr($row, 0, -2)] = substr($row, -1, 1);
					}
				}

				// XML Datei parsen
				foreach ($xml->item as $item) {
					if ((string) $item->file == 'entry') {
						foreach ($item->action as $action) {
							if ((string) $action->name == $this->action && (string) $action->level != '0' && isset($access_level[$module]) && (string) $action->level <= $access_level[$module]) {
								return true;
							}
						}
					} elseif ((string) $item->file == $page && (string) $item->level != '0' && isset($access_level[$module]) && (string) $item->level <= $access_level[$module]) {
						return true;
					}
				}
			}
		}
		return false;
	}
	/**
	 * Führt eine Suche durch, ob das gesuchte Modul aktiv ist
	 *
	 * @param string $module
	 * 	Das zu überprüfende Modul
	 * @return boolean
	 */
	function is_active($module)
	{
		$path = 'modules/' . $module;
		if (is_file($path . '/access.xml') && is_file($path . '/info.php')) {
			$xml = simplexml_load_file($path . '/access.xml');

			if ((string) $xml->active == '1') {
				return true;
			}
		}
		return false;
	}
}
?>