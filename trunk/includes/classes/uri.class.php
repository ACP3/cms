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

			if (seo::uriAliasExists($this->query))
				$this->redirect(seo::getUriAlias($this->query));

			// Nachschauen, ob ein URI-Alias für die aktuelle Seite festgelegt wurde
			$alias = $db->select('uri', 'seo', 'alias = \'' . $db->escape(substr($this->query, 0, -1)) . '\' OR alias = \'' . $db->escape(substr($this->query, 0, strpos($this->query, '/'))) . '\'');
			if (!empty($alias)) {
				$this->query = $alias[0]['uri'] . substr($this->query, strpos($this->query, '/', 1) + 1);
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
	 * Umleitung auf andere URLs
	 *
	 * @param string $args
	 *  Leitet auf eine interne ACP3 Seite weiter
	 * @param string $new_page
	 *  Leitet auf eine externe Seite weiter
	 */
	public function redirect($args, $new_page = 0)
	{
		if (!empty($args)) {
			if ($args == 'errors/404' || $args == 'errors/403')
				$args = (defined('IN_ACP3') ? '' : 'acp/') . $args;

			$protocol = empty($_SERVER['HTTPS']) || strtolower($_SERVER['HTTPS']) == 'off' ? 'http://' : 'https://';
			$host = $_SERVER['HTTP_HOST'];
			header('Location: ' . $protocol . $host . $this->route($args));
			exit;
		}
		header('Location:' . str_replace('&amp;', '&', $new_page));
		exit;
	}
	/**
	 * Generiert die ACP3 internen Hyperlinks
	 *
	 * @param string $uri
	 *  Inhalt der zu generierenden URL
	 * @param integer $alias
	 *	Gibt an, ob für die auszugebende Seite der URI-Alias ausgegeben werden soll,
	 *	falls dieser existiert
	 * @return string
	 */
	public function route($path, $alias = 0)
	{
		$path = $path . (!preg_match('/\/$/', $path) ? '/' : '');

		// Überprüfen, ob Alias vorhanden ist und diesen als URI verwenden
		if ($alias == 1 && !preg_match('/^acp\//', $path)) {
			global $uri;

			$alias = seo::getUriAlias($path);
			$path = $alias . (!preg_match('/\/$/', $alias) ? '/' : '');
		}
		$prefix = CONFIG_SEO_MOD_REWRITE == '0' || preg_match('/^acp\//', $path) ? PHP_SELF . '/' : ROOT_DIR;
		return $prefix . $path;
	}
}