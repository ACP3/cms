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
			$xml = simplexml_load_file('modules/' . $module . '/module.xml');

			if ((string) $xml->info->active == '1') {
				// Falls die einzelnen Zugriffslevel auf die Module noch nicht gesetzt sind, diese aus der Datenbank selektieren
				if (!isset($access_level[$module])) {
					$access_to_modules = $db->select('modules', 'access', 'id = \'' . $_SESSION['acp3_access'] . '\'');
					$modules = explode(',', $access_to_modules[0]['modules']);

					foreach ($modules as $row) {
						$access_level[substr($row, 0, -2)] = substr($row, -1, 1);
					}
				}

				// XML Datei parsen
				// Falls die entry.php eines Moduls verwendet werden soll, dann Zugriffslevel für die einzelnen Aktionen parsen
				if ($page == 'entry') {
					foreach ($xml->xpath('//access/item/action') as $action) {
						if ((string) $action->name == $this->action && (string) $action->level != '0' && isset($access_level[$module]) && (string) $action->level <= $access_level[$module]) {
							return true;
						}
					}
				// Restlichen Dateien durchlaufen
				} else {
					foreach ($xml->access->item as $item) {
						if ((string) $item->file == $page && (string) $item->level != '0' && isset($access_level[$module]) && (string) $item->level <= $access_level[$module]) {
							return true;
						}
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
	function modulesList()
	{
		$modules_dir = scandir('modules/');
		$mod_list = array();

		foreach ($modules_dir as $module) {
			$info = $this->parseInfo($module);
			if (is_array($info)) {
				$name = $info['name'];
				$mod_list[$name] = $info;
				$mod_list[$name]['dir'] = $module;
			}
		}
		ksort($mod_list);
		return $mod_list;
	}
	/**
	 * Durchläuft für das angeforderte Modul den <info> Abschnitt in der
	 * module.xml und gibt die gefunden Informationen als Array zurück
	 *
	 * @param string $module
	 * @return mixed
	 */
	function parseInfo($module)
	{
		$path = 'modules/' . $module . '/module.xml';
		if (!preg_match('=/=', $module) && is_file($path)) {
			$xml = simplexml_load_file($path);

			$info = $xml->info;

			$mod_info = array();
			$mod_info['author'] = (string) $info->author;
			$mod_info['description'] = (string) $info->description['lang'] == 'true' ? lang($module, 'mod_description') : (string) $info->description;
			$mod_info['name'] = (string) $info->name['lang'] == 'true' ? lang($module, $module) : (string) $info->name;
			$mod_info['version'] = (string) $info->version['core'] == 'true' ? CONFIG_VERSION : (string) $info->version;
			$mod_info['active'] = (string) $info->active;
			$mod_info['categories'] = isset($info->categories) ? true : false;
			$mod_info['protected'] = $info->protected ? true : false;
			return $mod_info;
		}
		return false;
	}
}
?>