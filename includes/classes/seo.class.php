<?php
/**
 * SEO
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */
/**
 * Klasse zum Setzen von URI Aliases, Keywords und Beschreibungen für Seiten
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */
class seo
{
	/**
	 * Caching Variable für die URI-Aliases
	 *
	 * @access private
	 * @var array
	 */
	private static $aliases = array();

	/**
	 * Setzt den Cache für die URI-Aliase
	 *
	 * @return boolean
	 */
	private static function setSEOCache()
	{
		global $db;

		$aliases = $db->select('uri, alias, keywords, description', 'seo');
		$c_aliases = count($aliases);
		$data = array();

		for ($i = 0; $i < $c_aliases; ++$i) {
			$data[$aliases[$i]['uri']] = array(
				'alias' => $aliases[$i]['alias'],
				'keywords' => $aliases[$i]['keywords'],
				'description' => $aliases[$i]['description']
			);
		}

		return cache::create('aliases', $data);
	}
	/**
	 * Gibt den Cache der URI-Aliase aus
	 *
	 * @return array
	 */
	private static function getSEOCache()
	{
		if (!cache::check('aliases'))
			self::setSEOCache();

		return cache::output('aliases');
	}
	/**
	 * Gibt die Keywords und Beschreibung der aktuell angezeigten Seite oder der
	 * Elternseite aus
	 *
	 * @param integer $mode
	 * @return string
	 */
	public static function getCurrentKeywordsOrDescription($mode = 1)
	{
		global $uri;

		if ($mode == 1) {
			$keywords = self::getKeywordsOrDescription($uri->query);
			if (empty($keywords)) {
				$keywords = self::getKeywordsOrDescription($uri->mod);
			}
			return !empty($keywords) ? $keywords : CONFIG_SEO_META_KEYWORDS;
		} else {
			$description = self::getKeywordsOrDescription($uri->query, 'description');
			if (empty($description)) {
				$description = self::getKeywordsOrDescription($uri->mod, 'description');
			}
			return !empty($description) ? $description : CONFIG_SEO_META_DESCRIPTION;
		}
	}
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

		return array_key_exists($path, self::$aliases) ? true : false;
	}
	/**
	 * Gibt einen URI-Alias aus
	 *
	 * @param string $path
	 * @return string
	 */
	public static function getUriAlias($path)
	{
		if (empty(self::$aliases))
			self::$aliases = self::getSEOCache();

		$path.= !preg_match('/\/$/', $path) ? '/' : '';

		return !empty(self::$aliases[$path]['alias']) ? self::$aliases[$path]['alias'] : $path;
	}
	/**
	 * Gibt die Schlüsselwörter oder Beschreibung der Seite aus
	 *
	 * @param string $path
	 * @param string $field
	 * @return string
	 */
	public static function getKeywordsOrDescription($path, $field = 'keywords')
	{
		if (empty(self::$aliases))
			self::$aliases = self::getSEOCache();

		$path.= !preg_match('/\/$/', $path) ? '/' : '';

		return !empty(self::$aliases[$path][$field]) ? self::$aliases[$path][$field] : '';
	}
	/**
	 * Trägt einen URI-Alias in die Datenbank ein bzw. aktualisiert den Eintrag
	 *
	 * @param string $alias
	 * @param string $path
	 * @param string $keywords
	 * @param string $description
	 * @return boolean
	 */
	public static function insertUriAlias($alias, $path, $keywords = '', $description = '')
	{
		global $db;

		$path.= !preg_match('/\/$/', $path) ? '/' : '';

		// Vorhandenen Alias aktualisieren bzw. wenn der Alias leer ist, diesen löschen
		if ($db->countRows('*', 'seo', 'uri = \'' . $db->escape($path) . '\'') == 1) {
			$bool = $db->update('seo', array('alias' => $alias, 'keywords' => $keywords, 'description' => $description), 'uri = \'' . $db->escape($path) . '\'');
		// Neuer Eintrag in DB
		} else {
			$bool = $db->insert('seo', array('alias' => $alias, 'uri' => $db->escape($path), 'keywords' => $keywords, 'description' => $description));
		}

		$bool2 = self::setSEOCache();
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

		$bool = $db->delete('seo', 'uri = \'' . $db->escape($path) . '\'');
		$bool2 = self::setSEOCache();
		return $bool && $bool2 ? true : false;
	}
}