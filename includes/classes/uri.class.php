<?php
/**
 * URI
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */

if (defined('IN_ACP3') === false)
	exit;

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
	private $params = array();
	/**
	 * Die komplette übergebene URL
	 *
	 * @var string
	 */
	public $query = '';

	/**
	 * Zerlegt u.a. die übergebenen Parameter in der URI in ihre Bestandteile
	 */
	function __construct($defaultModule = '', $defaultFile = '')
	{
		$this->query = substr(str_replace(PHP_SELF, '', htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES)), 1);
		$this->query.= !preg_match('/\/$/', $this->query) ? '/' : '';

		if (preg_match('/^(acp\/)/', $this->query)) {
			// Definieren, dass man sich im Administrationsbereich befindet
			define('IN_ADM', true);
			// "acp/" entfernen
			$this->query = substr($this->query, 4);
		} elseif (defined('IN_INSTALL') === false) {
			// Query auf eine benutzerdefinierte Startseite setzen
			if ($this->query === '/' && CONFIG_HOMEPAGE !== '')
				$this->query = CONFIG_HOMEPAGE;

			// Nur ausführen, falls URI-Aliase aktiviert sind
			if (CONFIG_SEO_ALIASES === true) {
				// Falls für Query ein Alias existiert, zu diesem weiterleiten
				if (seo::uriAliasExists($this->query) === true)
					$this->redirect(seo::getUriAlias($this->query), 0, 1);

				// Annehmen, dass ein URI Alias mit zusätzlichen Parametern übergeben wurde
				if (preg_match('/^([a-z]{1}[a-z\d\-]*\/)+(([a-z\d\-]+)_(.+)\/)+$/', $this->query)) {
					$query = preg_split('=/=', $this->query, -1, PREG_SPLIT_NO_EMPTY);
					// Annahme bestätigt
					if (is_file(MODULES_DIR . $query[0] . '/' . $query[1] . '.php') === false) {
						$length = 0;
						foreach ($query as $row) {
							if (strpos($row, '_') === false) {
								$length+= strlen($row) + 1;
							} else {
								break;
							}
						}
						$params = substr($this->query, $length);
						$this->query = substr($this->query, 0, $length);
					}
				}

				global $db;

				// Nachschauen, ob ein URI-Alias für die aktuelle Seite festgelegt wurde
				$alias = $db->select('uri', 'seo', 'alias = \'' . $db->escape(substr($this->query, 0, -1)) . '\'');
				if (!empty($alias)) {
					$this->query = $alias[0]['uri'] . (!empty($params) ? $params : '');
				}
			}
		}

		$query = preg_split('=/=', $this->query, -1, PREG_SPLIT_NO_EMPTY);

		if (empty($defaultModule) || empty($defaultFile)) {
			$defaultModule = defined('IN_ADM') ? 'acp' : 'news';
			$defaultFile = defined('IN_ADM') ? 'adm_list' : 'list';
		}

		$this->mod = !empty($query[0]) ? $query[0] : $defaultModule;
		$this->file = !empty($query[1]) ? $query[1] : $defaultFile;

		if (!empty($query[2])) {
			$c_query = count($query);

			for ($i = 2; $i < $c_query; ++$i) {
				// Position
				if (defined('POS') === false && preg_match('/^(page_(\d+))$/', $query[$i])) {
					global $auth;
					define('POS', (substr($query[$i], 5) - 1) * $auth->entries);
					$this->page = (int) substr($query[$i], 5);
				// ID eines Datensatzes
				} elseif (preg_match('/^(id_(\d+))$/', $query[$i])) {
					$this->id = (int) substr($query[$i], 3);
				// Additional URI parameters
				} elseif (preg_match('/^(([a-z0-9-]+)_(.+))$/', $query[$i])) {
					$param = explode('_', $query[$i], 2);
					$this->$param[0] = $param[1];
				}
			}
		}

		if (!empty($_POST['cat']) && validate::isNumber($_POST['cat']) === true)
			$this->cat = $_POST['cat'];
		if (!empty($_POST['action']))
			$this->action = $_POST['action'];
		if (defined('POS') === false)
			define('POS', '0');
	}
	/**
	 * Gibt einen URI Parameter aus
	 *
	 * @param string $key
	 * @return string|integer|null
	 */
	public function __get($key)
	{
		return isset($this->params[$key]) === true ? $this->params[$key] : null;
	}
	/**
	 * Setzt einen neuen URI Parameter
	 *
	 * @param string $name
	 * @param string|integer $value
	 */
	public function __set($name, $value)
	{
		// Parameter sollten nicht überschrieben werden können
		if (isset($this->params[$name]) === false)
			$this->params[$name] = $value;
	}
	/**
	 * Gibt die URI-Parameter aus
	 *
	 * @return array
	 */
	public function getParameters()
	{
		return $this->params;
	}
	/**
	 * Umleitung auf andere URLs
	 *
	 * @param string $args
	 *  Leitet auf eine interne ACP3 Seite weiter
	 * @param string $new_page
	 *  Leitet auf eine externe Seite weiter
	 */
	public function redirect($args, $new_page = 0, $moved_permanently = 0)
	{
		if (!empty($args)) {
			if ($args === 'errors/404' || $args === 'errors/403')
				$args = (defined('IN_ADM') === false ? '' : 'acp/') . $args;

			$protocol = empty($_SERVER['HTTPS']) || strtolower($_SERVER['HTTPS']) === 'off' ? 'http://' : 'https://';
			$host = $_SERVER['HTTP_HOST'];
			if ($moved_permanently === 1) {
				header('HTTP/1.0 301 Moved Permanently');
			}
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
		if (CONFIG_SEO_ALIASES === true && $alias === 1 && !preg_match('/^acp\//', $path)) {
			$alias = seo::getUriAlias($path);
			$path = $alias . (!preg_match('/\/$/', $alias) ? '/' : '');
		}
		$prefix = CONFIG_SEO_MOD_REWRITE === false || preg_match('/^acp\//', $path) ? PHP_SELF . '/' : ROOT_DIR;
		return $prefix . $path;
	}
}