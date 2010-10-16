<?php
/**
 * URI
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */
/**
 * Verarbeitet die URI Query
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */
class uri
{
	/**
	 * Array, welches die URI Parameter enthält
	 *
	 * @var array
	 * @access protected
	 */
	public $params = array();
	/**
	 * Die komplette übergebene URL
	 *
	 * @var string
	 */
	public $query = '';

	/**
	 * Zerlegt u.a. die übergebenen Parameter in der URI in ihre Bestandteile
	 *
	 * @return modules
	 */
	function __construct()
	{
		$this->query = substr(str_replace(PHP_SELF, '', htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES)), 1);
		$this->query.= !preg_match('/\/$/', $this->query) ? '/' : '';

		if (preg_match('/^(acp\/)/', $this->query)) {
			// Definieren, dass man sich im Administrationsbereich befindet
			define('IN_ADM', true);
			// "acp/" entfernen
			$this->query = substr($this->query, 4);
		} elseif (!defined('IN_INSTALL')) {
			global $db;

			// Definieren, dass man sich im Frontend befindet
			define('IN_ACP3', true);
			// Query auf eine benutzerdefinierte Startseite setzen
			if ($this->query == '/' && CONFIG_HOMEPAGE != '') {
				$this->query = CONFIG_HOMEPAGE;
			}

			// Nachschauen, ob ein URI-Alias für die aktuelle Seite festgelegt wurde
			$alias = $db->select('uri', 'aliases', 'alias = \'' . db::escape(substr($this->query, 0, -1)) . '\'');
			if (!empty($alias)) {
				$this->query = $alias[0]['uri'];
			}
		}

		$query = preg_split('=/=', $this->query, -1, PREG_SPLIT_NO_EMPTY);
		$defaultModule = defined('IN_ADM') ? 'acp' : 'news';
		$defaultPage = defined('IN_ADM') ? 'adm_list' : 'list';

		$this->mod = !empty($query[0]) ? $query[0] : $defaultModule;
		$this->page = !empty($query[1]) ? $query[1] : $defaultPage;

		if (!empty($query[2])) {
			$c_query = count($query);

			for ($i = 2; $i < $c_query; ++$i) {
				// Position
				if (!defined('POS') && preg_match('/^(pos_(\d+))$/', $query[$i])) {
					define('POS', substr($query[$i], 4));
				// Additional URI parameters
				} elseif (preg_match('/^(([a-z0-9-]+)_(.+))$/', $query[$i])) {
					$param = explode('_', $query[$i], 2);
					$this->$param[0] = $param[1];
				}
			}
		}

		if (!empty($_POST['cat']))
			$this->cat = $_POST['cat'];
		if (!empty($_POST['action']))
			$this->action = $_POST['action'];
		if (!defined('POS'))
			define('POS', '0');
	}
	/**
	 * Gibt einen URI Parameter aus
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function __get($key)
	{
		if (!empty($key) && array_key_exists($key, $this->params))
			return $this->params[$key];
		return null;
	}
	/**
	 * Setzt einen neuen URI Parameter
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	public function __set($name, $value)
	{
		$this->params[$name] = $value;
	}
	/**
	 * Setzt den Cache für die URI-Aliase
	 *
	 * @return boolean
	 */
	public static function setAliasCache()
	{
		global $db;

		$aliases = $db->select('uri, alias', 'aliases');
		$c_aliases = count($aliases);
		$data = array();

		for ($i = 0; $i < $c_aliases; ++$i) {
			$data[$aliases[$i]['uri']] = $aliases[$i]['alias'];
		}

		return cache::create('aliases', $data);
	}
	/**
	 * Gibt den Cache der URI-Aliase aus
	 *
	 * @return array
	 */
	public static function getAliasCache()
	{
		if (!cache::check('aliases'))
			self::setAliasCache();

		return cache::output('aliases');
	}
	/**
	 * Gibt einen URI-Alias aus
	 *
	 * @param string $path
	 * @return string
	 */
	public static function getUriAlias($path)
	{
		static $aliases = array();

		if (empty($aliases)) {
			$aliases = self::getAliasCache();
		}

		$path.= !preg_match('/\/$/', $path) ? '/' : '';

		return !empty($aliases[$path]) ? $aliases[$path] : $path;
	}
	/**
	 * Trägt einen URI-Alias in die Datenbank ein bzw. aktualisiert den Eintrag
	 *
	 * @param string $alias
	 * @param string $path
	 * @return boolean
	 */
	public static function insertUriAlias($alias, $path)
	{
		global $db;

		$path.= !preg_match('/\/$/', $path) ? '/' : '';

		// Vorhandenen Alias aktualisieren bzw. wenn der Alias leer ist, diesen löschen
		if ($db->countRows('*', 'aliases', 'uri = \'' . db::escape($path) . '\'') == 1) {
			if ($alias == '') {
				$bool = self::deleteUriAlias($path);
			} else {
				$bool = $db->update('aliases', array('alias' => $alias), 'uri = \'' . db::escape($path) . '\'');
			}
		// Neuer Eintrag in DB
		} else {
			$bool = $db->insert('aliases', array('alias' => $alias, 'uri' => db::escape($path)));
		}

		$bool2 = self::setAliasCache();
		return $bool && $bool2 ? true : false;
	}
	/**
	 * Löscht einen URI-Alias
	 *
	 * @param string $alias
	 * @param string $path
	 * @return boolean
	 */
	public static function deleteUriAlias($path)
	{
		global $db;

		$path.= !preg_match('/\/$/', $path) ? '/' : '';

		$bool = $db->delete('aliases', 'uri = \'' . db::escape($path) . '\'');
		$bool2 = self::setAliasCache();
		return $bool && $bool2 ? true : false;
	}
}