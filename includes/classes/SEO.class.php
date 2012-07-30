<?php
/**
 * SEO
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Core
 */

if (defined('IN_ACP3') === false)
	exit;

/**
 * Klasse zum Setzen von URI Aliases, Keywords und Beschreibungen für Seiten
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Core
 */
class ACP3_SEO
{
	/**
	 * Caching Variable für die URI-Aliases
	 *
	 * @access private
	 * @var array
	 */
	private static $aliases = array();
	/**
	 * Gibt die nächste Seite an
	 * 
	 * @var string 
	 */
	private static $next_page = '';
	/**
	 * Gibt die vorherige Seite an
	 *
	 * @var string 
	 */
	private static $previous_page = '';

	/**
	 * Setzt den Cache für die URI-Aliase
	 *
	 * @return boolean
	 */
	private static function setSEOCache()
	{
		global $db;

		$aliases = $db->select('uri, alias, keywords, description, robots', 'seo');
		$c_aliases = count($aliases);
		$data = array();

		for ($i = 0; $i < $c_aliases; ++$i) {
			$data[$aliases[$i]['uri']] = array(
				'alias' => $aliases[$i]['alias'],
				'keywords' => $aliases[$i]['keywords'],
				'description' => $aliases[$i]['description'],
				'robots' => $aliases[$i]['robots']
			);
		}

		return ACP3_Cache::create('aliases', $data);
	}
	/**
	 * Gibt den Cache der URI-Aliase aus
	 *
	 * @return array
	 */
	private static function getSEOCache()
	{
		if (ACP3_Cache::check('aliases') === false)
			self::setSEOCache();

		return ACP3_Cache::output('aliases');
	}
	/**
	 * Gibt die für die jeweilige Seite gesetzten Metatags aus
	 *
	 * @return string 
	 */
	public static function getMetaTags()
	{
		global $tpl;

		$meta = array(
			'description' => self::getCurrentDescription(),
			'keywords' => self::getCurrentKeywords(),
			'robots' => defined('IN_ADM') === true ? 'noindex,nofollow' : self::getCurrentRobotsSetting(),
			'previous_page' => self::$previous_page,
			'next_page' => self::$next_page,
		);
		$tpl->assign('meta', $meta);

		return ACP3_View::fetchTemplate('common/meta.tpl');
	}
	/**
	 * Gibt die Beschreibung der aktuell angezeigten Seite oder der
	 * Elternseite aus
	 *
	 * @return string
	 */
	public static function getCurrentDescription()
	{
		global $uri;

		$description = self::getDescription($uri->getCleanQuery());
		if (empty($description))
			$description = self::getDescription($uri->mod);

		return !empty($description) ? $description : CONFIG_SEO_META_DESCRIPTION;
	}
	/**
	 * Gibt die Keywords der aktuell angezeigten Seite oder der
	 * Elternseite aus
	 *
	 * @return string
	 */
	public static function getCurrentKeywords()
	{
		global $uri;

		$keywords = self::getKeywords($uri->getCleanQuery());
		if (empty($keywords))
			$keywords = self::getKeywords($uri->mod);

		return strtolower(!empty($keywords) ? $keywords : CONFIG_SEO_META_KEYWORDS);
	}
	/**
	 * Gibt den Robots-Metatag der aktuell angezeigten Seite oder der
	 * Elternseite aus
	 *
	 * @return string 
	 */
	public static function getCurrentRobotsSetting()
	{
		global $uri;

		$robots = self::getRobotsSetting($uri->getCleanQuery());
		if (empty($robots))
			$robots = self::getRobotsSetting($uri->mod);

		return strtolower(!empty($robots) ? $robots : self::getRobotsSetting());
	}
	/**
	 * Gibt die Beschreibung der Seite aus
	 *
	 * @param string $path
	 * @return string
	 */
	public static function getDescription($path)
	{
		if (empty(self::$aliases))
			self::$aliases = self::getSEOCache();

		$path.= !preg_match('/\/$/', $path) ? '/' : '';

		return !empty(self::$aliases[$path]['description']) ? self::$aliases[$path]['description'] : '';
	}
	/**
	 * Gibt die Schlüsselwörter der Seite aus
	 *
	 * @param string $path
	 * @return string
	 */
	public static function getKeywords($path)
	{
		if (empty(self::$aliases))
			self::$aliases = self::getSEOCache();

		$path.= !preg_match('/\/$/', $path) ? '/' : '';

		return !empty(self::$aliases[$path]['keywords']) ? self::$aliases[$path]['keywords'] : '';
	}
	/**
	 * Gibt die jeweilige Einstellung für den Robots-Metatag aus
	 *
	 * @param string $path
	 * @return string 
	 */
	public static function getRobotsSetting($path = '')
	{
		$replace = array(
			1 => 'index,follow',
			2 => 'index,nofollow',
			3 => 'noindex,follow',
			4 => 'noindex,nofollow',
		);

		if ($path === '') {
			return strtr(CONFIG_SEO_ROBOTS, $replace);
		} else {
			if (empty(self::$aliases))
				self::$aliases = self::getSEOCache();

			$path.= !preg_match('/\/$/', $path) ? '/' : '';

			return strtr(isset(self::$aliases[$path]) === false || self::$aliases[$path]['robots'] == 0 ? CONFIG_SEO_ROBOTS : self::$aliases[$path]['robots'], $replace);
		}
	}
	/**
	 * Gibt einen URI-Alias aus
	 *
	 * @param string $path
	 * @return string
	 */
	public static function getUriAlias($path, $for_form = false)
	{
		if (empty(self::$aliases))
			self::$aliases = self::getSEOCache();

		$path.= !preg_match('/\/$/', $path) ? '/' : '';

		return !empty(self::$aliases[$path]['alias']) ? self::$aliases[$path]['alias'] : ($for_form === true ? '' : $path);
	}
	/**
	 * Setzt die nächste Seite
	 *
	 * @param string $path 
	 */
	public static function setNextPage($path)
	{
		self::$next_page = $path;
	}
	/**
	 * Setzt die vorherige Seite
	 *
	 * @param string $path 
	 */
	public static function setPreviousPage($path)
	{
		self::$previous_page = $path;
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

		$bool = $db->delete('seo', 'uri = \'' . $db->escape($path) . '\'');
		$bool2 = self::setSEOCache();
		return $bool !== false && $bool2 !== false ? true : false;
	}
	/**
	 * Trägt einen URI-Alias in die Datenbank ein bzw. aktualisiert den Eintrag
	 *
	 * @param string $path
	 * @param string $alias
	 * @param string $keywords
	 * @param string $description
	 * @return boolean
	 */
	public static function insertUriAlias($path, $alias, $keywords = '', $description = '', $robots = '')
	{
		global $db;

		$path.= !preg_match('/\/$/', $path) ? '/' : '';

		// Vorhandenen Alias aktualisieren
		if ($db->countRows('*', 'seo', 'uri = \'' . $db->escape($path) . '\'') == 1) {
			$bool = $db->update('seo', array('alias' => $alias, 'keywords' => $keywords, 'description' => $description, 'robots' => $robots), 'uri = \'' . $db->escape($path) . '\'');
		// Neuer Eintrag in DB
		} else {
			$bool = $db->insert('seo', array('alias' => $alias, 'uri' => $db->escape($path), 'keywords' => $keywords, 'description' => $description, 'robots' => $robots));
		}

		$bool2 = self::setSEOCache();
		return $bool !== false && $bool2 !== false ? true : false;
	}
	/**
	 * Gibt die Formularfelder für die Suchmaschinenoptimierung aus
	 *
	 * @param string $alias
	 * @param string $keywords
	 * @param string $description
	 * @param string $robots
	 * @return string
	 */
	public static function formFields($path = '')
	{
		global $lang, $tpl;

		if (!empty($path)) {
			$path.= !preg_match('/\/$/', $path) ? '/' : '';

			$alias = isset($_POST['alias']) ? $_POST['alias'] : ACP3_SEO::getUriAlias($path, true);
			$keywords = isset($_POST['seo_keywords']) ? $_POST['seo_keywords'] : ACP3_SEO::getKeywords($path);
			$description = isset($_POST['seo_description']) ? $_POST['seo_description'] : ACP3_SEO::getDescription($path);
			$robots = isset(self::$aliases[$path]) === true ? self::$aliases[$path]['robots'] : 0;
		} else {
			$alias = $keywords = $description = '';
			$robots = 0;
		}

		$seo = array(
			'enable_uri_aliases' => CONFIG_SEO_ALIASES,
			'alias' => isset($alias) ? $alias : '',
			'keywords' => $keywords,
			'description' => $description,
			'robots' => array(
				array(
					'value' => 0,
					'selected' => selectEntry('seo_robots', 0, $robots),
					'lang' => sprintf($lang->t('common', 'seo_robots_use_system_default'), ACP3_SEO::getRobotsSetting())
				),
				array(
					'value' => 1,
					'selected' => selectEntry('seo_robots', 1, $robots),
					'lang' => $lang->t('common', 'seo_robots_index_follow')
				),
				array(
					'value' => 2,
					'selected' => selectEntry('seo_robots', 2, $robots),
					'lang' => $lang->t('common', 'seo_robots_index_nofollow')
				),
				array(
					'value' => 3,
					'selected' => selectEntry('seo_robots', 3, $robots),
					'lang' => $lang->t('common', 'seo_robots_noindex_follow')
				),
				array(
					'value' => 4,
					'selected' => selectEntry('seo_robots', 4, $robots),
					'lang' => $lang->t('common', 'seo_robots_noindex_nofollow')
				)
			)
		);

		$tpl->assign('seo', $seo);
		return ACP3_View::fetchTemplate('common/seo_fields.tpl');	}
	/**
	 * Überprüft, ob ein URI-Alias existiert
	 *
	 * @param string $path
	 * @return boolean
	 */
	public static function uriAliasExists($path)
	{
		if (empty(self::$aliases))
			self::$aliases = self::getSEOCache();

		$path.= !preg_match('/\/$/', $path) ? '/' : '';

		return array_key_exists($path, self::$aliases) === true && !empty(self::$aliases[$path]['alias']);
	}
}