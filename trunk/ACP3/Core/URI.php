<?php
/**
 * URI
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Core
 */

namespace ACP3\Core;

/**
 * Verarbeitet die URI Query
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Core
 */
class URI
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
		// Minify von der URI-Verarbeitung ausschließen
		if ((bool) preg_match('=(includes/min/index\.php|libraries/kcfinder)=', $_SERVER['PHP_SELF']) === false) {
			$this->preprocessUriQuery();
			if (defined('IN_INSTALL') === false) {
				// Query auf eine benutzerdefinierte Startseite setzen
				if ($this->query === '/' && CONFIG_HOMEPAGE !== '')
					$this->query = CONFIG_HOMEPAGE;
				$this->checkForUriAlias();
			}

			$this->setUriParameters($defaultModule, $defaultFile);
		}
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
	 * @param string $key
	 * @param string|integer $value
	 */
	public function __set($key, $value)
	{
		// Parameter sollten nicht überschrieben werden können
		if (isset($this->params[$key]) === false)
			$this->params[$key] = $value;
	}
	/**
	 * Überprüft, ob ein URI-Parameter existiert
	 *
	 * @param string $key
	 * @return boolean
	 */
	public function __isset($key)
	{
		return isset($this->params[$key]);
	}
	/**
	 * Grundlegende Verarbeitung der URI-Query 
	 */
	private function preprocessUriQuery()
	{
		$this->query = substr(str_replace(PHP_SELF, '', htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES)), 1);
		$this->query.= !preg_match('/\/$/', $this->query) ? '/' : '';

		if (preg_match('/^(acp\/)/', $this->query)) {
			// Definieren, dass man sich im Administrationsbereich befindet
			define('IN_ADM', true);
			// "acp/" entfernen
			$this->query = substr($this->query, 4);
		}

		return;
	}
	/**
	 * Überprüft die URI auf einen möglichen URI-Alias und
	 * macht im Erfolgsfall einen Redirect darauf
	 *
	 * @return
	 */
	private function checkForUriAlias()
	{
		// Nur ausführen, falls URI-Aliase aktiviert sind
		if ((bool) (bool) CONFIG_SEO_ALIASES === true && !defined('IN_ADM')) {
			// Falls für Query ein Alias existiert, zu diesem weiterleiten
			if (SEO::uriAliasExists($this->query) === true)
				// URI-Alias wird von uri::route() erzeugt
				$this->redirect($this->query, 0, 1);

			// Annehmen, dass ein URI Alias mit zusätzlichen Parametern übergeben wurde
			if (preg_match('/^([a-z]{1}[a-z\d\-]*\/)+(([a-z\d\-]+)_(.+)\/)+$/', $this->query)) {
				$query = preg_split('=/=', $this->query, -1, PREG_SPLIT_NO_EMPTY);
				$className = "\\ACP3\\Modules\\" . ucfirst($query[0]) . "\\" . ucfirst($query[0]) . 'Frontend';
				// Keine entsprechende Module-Action gefunden -> muss Alias sein
				if (method_exists($className, 'action' . ucfirst($query[1])) === false) {
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

			// Nachschauen, ob ein URI-Alias für die aktuelle Seite festgelegt wurde
			$alias = \ACP3\CMS::$injector['Db']->fetchAssoc('SELECT uri FROM ' . DB_PRE . 'seo WHERE alias = ?', array(substr($this->query, 0, -1)));
			if (!empty($alias)) {
				$this->query = $alias['uri'] . (!empty($params) ? $params : '');
			}
		}

		return;
	}
	/**
	 * Setzt alle in URI::query enthaltenen Parameter
	 *
	 * @param string $defaultModule
	 * @param string $defaultFile
	 * @return
	 */
	private function setUriParameters($defaultModule, $defaultFile)
	{
		$query = preg_split('=/=', $this->query, -1, PREG_SPLIT_NO_EMPTY);

		if (empty($defaultModule) && empty($defaultFile)) {
			$defaultModule = defined('IN_ADM') ? 'acp' : 'news';
			$defaultFile = 'list';
		}

		$this->mod = isset($query[0]) ? $query[0] : $defaultModule;
		$this->file = (defined('IN_ADM') ? 'acp_' : '') . (isset($query[1]) ? $query[1] : $defaultFile);

		// Redirect, falls jemand versuchen sollte eine ACP-Seite ohne
		// vorangestelltes "acp/" aufzurufen, anstatt eine leere Seite anzuzeigen
		if (defined('IN_ADM') === false && strpos($this->file, 'acp_') === 0) {
			$params = '';
			for ($i = 2; isset($query[$i]); ++$i) {
				$params.= '/' . $query[$i];
			}

			$this->redirect('acp/' . $this->mod . '/' . substr($this->file, 4) . $params);
		}

		if (isset($query[2])) {
			$c_query = count($query);

			for ($i = 2; $i < $c_query; ++$i) {
				// Position
				if (preg_match('/^(page_(\d+))$/', $query[$i])) {
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
		// Workaround für Securitytoken-Generierung,
		// falls die URL nur aus dem Modulnamen besteht
		} elseif (isset($query[0]) && !isset($query[1])) {
			$this->query.= $defaultFile . '/';
		} elseif (!isset($query[0]) && !isset($query[1])) {
			$this->query = $defaultModule . '/' . $defaultFile . '/';
		}

		if (!empty($_POST['cat']) && Validate::isNumber($_POST['cat']) === true)
			$this->cat = (int) $_POST['cat'];
		if (!empty($_POST['action']))
			$this->action = $_POST['action'];

		return;
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
	 * Gibt die bereinigte URI-Query aus, d.h. ohne die anzuzeigende Seite
	 *
	 * @return string 
	 */
	public function getCleanQuery()
	{
		return preg_replace('/\/page_(\d+)\//', '/', $this->query);
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
			$protocol = empty($_SERVER['HTTPS']) || strtolower($_SERVER['HTTPS']) === 'off' ? 'http://' : 'https://';
			$host = $_SERVER['HTTP_HOST'];
			if ($moved_permanently === 1) {
				header('HTTP/1.1 301 Moved Permanently');
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
	public function route($path, $alias = 1)
	{
		$path = $path . (!preg_match('/\/$/', $path) ? '/' : '');

		if (!preg_match('/^acp\//', $path)) {
			if (count(preg_split('=/=', $path, -1, PREG_SPLIT_NO_EMPTY)) === 1)
				$path.= 'list/';
			// Überprüfen, ob Alias vorhanden ist und diesen als URI verwenden
			if ((bool) (bool) CONFIG_SEO_ALIASES === true && $alias === 1) {
				$alias = SEO::getUriAlias($path);
				$path = $alias . (!preg_match('/\/$/', $alias) ? '/' : '');
			}
		}
		$prefix = (bool) CONFIG_SEO_MOD_REWRITE === false || preg_match('/^acp\//', $path) || defined('IN_UPDATER') === true ? PHP_SELF . '/' : ROOT_DIR;
		return $prefix . $path;
	}
}