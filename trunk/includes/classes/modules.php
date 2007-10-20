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
	 * Definieren, ob man sich in der Administration befindet, oder nicht
	 *
	 * @var boolean
	 * @access public
	 */
	public $acp = false;
	/**
	 * Die Aktion, welche z.B. in einem Formular ausgeführt werden soll
	 *
	 * @var string
	 * @access public
	 */
	public $action = '';
	/**
	 * Die ID einer Kategorie in der Datenbank
	 *
	 * @var integer
	 * @access public
	 */
	public $cat = '0';
	/**
	 * Die ID eines Eintrages in der Datenbank
	 *
	 * @var integer
	 * @access public
	 */
	public $id = '0';
	/**
	 * Die restlichen URI Parameter
	 *
	 * @var array
	 * @access public
	 */
	public $gen = array();

	/**
	 * Zerlegt u.a. die übergebenen Parameter in der URL in ihre Bestandteile
	 *
	 * @return modules
	 */
	function __construct()
	{
		$query = !empty($_GET['stm']) ? explode('/', $_GET['stm']) : 0;
		if (isset($query[1]) && strpos($query[1], 'acp_') !== false) {
			$this->acp = true;

			//define('CUSTOM_LAYOUT', 'acp.html');
			$default_page = 'acp_list';
		} else {
			$default_page = 'list';
		}
		$this->mod = !empty($query[0]) ? $query[0] : 'news';
		$this->page = !empty($query[1]) ? $query[1] : $default_page;

		$this->cat = !empty($_POST['cat']) ? $_POST['cat'] : '0';
		$this->action = !empty($_POST['action']) ? $_POST['action'] : $this->page;

		if (!empty($query[2])) {
			$c_stm = count($query);

			// Regex
			$pos_regex = '/^(pos_(\d+))$/';
			$id_regex = '/^(id_(\d+))$/';
			$cat_regex = '/^(cat_(\d+))$/';
			$action_regex = '/^(action_(\w+))$/';
			$gen_regex = '/^(([a-z0-9-]+)_(.+))$/';

			for ($i = 2; $i < $c_stm; $i++) {
				if (!empty($query[$i])) {
					if (!defined('POS') && preg_match($pos_regex, $query[$i])) {
						define('POS', substr($query[$i], 4));
					} elseif (preg_match($id_regex, $query[$i])) {
						$this->id = substr($query[$i], 3);
					} elseif (preg_match($cat_regex, $query[$i])) {
						$this->cat = substr($query[$i], 4);
					} elseif (preg_match($action_regex, $query[$i])) {
						$this->action = substr($query[$i], 7);
					} elseif (preg_match($gen_regex, $query[$i])) {
						$pos = strpos($query[$i], '_');
						$this->gen[substr($query[$i], 0, $pos)] = substr($query[$i], $pos + 1, strlen($query[$i]));
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
	function check($module = 0, $page = 0, $area = 0) {
		global $auth, $db;
		static $access_level = array();

		$module = !empty($module) ? $module : $this->mod;
		$page = !empty($page) ? $page : $this->page;

		if (empty($area)) {
			$area = $this->acp ? 'acp' : 'frontend';
		}

		if (is_file('modules/' . $module . '/' . $page . '.php')) {
			$xml = simplexml_load_file('modules/' . $module . '/module.xml');

			if ((string) $xml->info->active == '1') {
				// Falls die einzelnen Zugriffslevel auf die Module noch nicht gesetzt sind, diese aus der Datenbank selektieren
				if (!isset($access_level[$module])) {
					// Zugriffslevel für Gäste
					$access_id = 2;
					// Zugriffslevel für Benutzer holen
					if (isset($_SESSION['acp3_id'])) {
						$info = $auth->getUserInfo('access');
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
				// Falls die entry.php eines Moduls verwendet werden soll, dann Zugriffslevel für die einzelnen Aktionen parsen
				if ($page == 'entry') {
					foreach ($xml->xpath('//access/entry/action') as $action) {
						if (isset($access_level[$module]) &&
							(string) $action->level != '0' &&
							(string) $action->level <= $access_level[$module] &&
							(string) $action->name == $this->action) {
							return true;
						}
					}
				// Restlichen Dateien durchlaufen
				} else {
					foreach ($xml->xpath('//access/' . $area . '/item') as $item) {
						if (isset($access_level[$module]) &&
							(string) $item->level != '0' &&
							(string) $item->level <= $access_level[$module] &&
							(string) $item->file == $page) {
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
				$mod_list[$info['name']] = $info;
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
			$mod_info['dir'] = $module;
			$mod_info['author'] = (string) $info->author;
			$mod_info['description'] = (string) $info->description['lang'] == 'true' ? lang($module, 'mod_description') : (string) $info->description;
			$mod_info['name'] = (string) $info->name['lang'] == 'true' ? lang($module, $module) : (string) $info->name;
			$mod_info['version'] = (string) $info->version['core'] == 'true' ? CONFIG_VERSION : (string) $info->version;
			$mod_info['active'] = (string) $info->active;
			$mod_info['tables'] = isset($info->tables) ? explode(',', (string) $info->tables) : false;
			$mod_info['categories'] = isset($info->categories) ? true : false;
			$mod_info['protected'] = $info->protected ? true : false;
			return $mod_info;
		}
		return false;
	}
}
?>