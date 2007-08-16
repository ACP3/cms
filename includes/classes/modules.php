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
	 * @param integer $mode
	 * 	1 = Nur überprüfen, ob Modul aktiv ist und Moduldatei existiert
	 * 	2 = Zusätzlich überprüfen, ob Benutzer auch Zugriff auf das Modul besitzt
	 * @param string $mod
	 * 	Zu überprüfendes Modul
	 * @param string $page
	 * 	Zu überprüfende Moduldatei
	 * @return boolean
	 */
	function check($mode = 2, $mod = 0, $page = 0)
	{
		global $db;

		$mod = !empty($mod) ? $mod : $this->mod;
		$page = !empty($page) ? $page : $this->page;

		$bool = $db->select('id', 'modules', 'module = \'' . $mod . '\' AND active = \'1\'', 0, 0, 0, 1) == '1' && is_file('modules/' . $mod . '/' . $page . '.php') ? true : false;
		if ($bool && $mode == 2 && isset($_SESSION) && ereg('[0-9]', $_SESSION['acp3_access'])) {
			$access = $db->select('mods', 'access', 'id = \'' . $_SESSION['acp3_access'] . '\'');

			if (count($access) > 0) {
				$mods = explode('|', $access[0]['mods']);
				$c_mods = count($mods);
				for ($i = 0; $i < $c_mods; $i++) {
					if ($mods[$i] == $mod) {
						return true;
					}
				}
			}
		}
		return $mode == 1 && $bool ? true : false;
	}
}
?>