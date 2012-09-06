<?php
/**
 * Language
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Core
 */

/**
 * Stellt Funktionen bereit, um das ACP3 in verschiedene Sprachen zu übersetzen
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Core
 */
class ACP3_Lang
{
	/**
	 * Die zur Zeit eingestellte Sprache
	 *
	 * @var string
	 * @access private
	 */
	private $lang = '';

	/**
	 *
	 * @var array
	 * @access private
	 */
	private $cache = array();

	function __construct()
	{
		$lang = ACP3_CMS::$auth->getUserLanguage();
		$this->lang = $this->languagePackExists($lang) === true ? $lang : CONFIG_LANG;
	}

	/**
	 * Gibt die aktuell eingestellte Sprache zurück
	 *
	 * @return string
	 */
	public function getLanguage()
	{
		return $this->lang;
	}

	/**
	 * Verändert die aktuell eingestellte Sprache
	 *
	 * @param string $lang
	 */
	public function setLanguage($lang)
	{
		if ($this->languagePackExists($lang) === true) {
			$this->lang = $lang;
			$this->cache = array();
		}
	}

	/**
	 * Cached die Sprachfiles, um diese schneller verarbeiten zu können
	 */
	public function setLanguageCache()
	{
		$data = array();
		$path = ACP3_ROOT . 'languages/' . $this->lang . '/';
		$dir = scandir($path);
		foreach ($dir as $module) {
			if ((bool) preg_match('/\.xml$/', $module) === true) {
				$xml = simplexml_load_file($path . $module);
				// Über die einzelnen Sprachstrings iterieren
				foreach ($xml->item as $item) {
					$data[substr($module, 0, -4)][(string) $item['key']] = (string) $item;
				}
			}
		}
		
		$this->cache = array();

		return ACP3_Cache::create('language_' . $this->lang, $data);
	}

	/**
	 * Gibt die gecacheten Sprachstrings aus
	 *
	 * @return array
	 */
	private function getLanguageCache()
	{
		$filename = 'language_' . $this->lang;
		if (ACP3_Cache::check($filename) === false)
			$this->setLanguageCache();

		return ACP3_Cache::output($filename);
	}

	/**
	 * Gibt den angeforderten Sprachstring aus
	 *
	 * @param string $module
	 * @param string $key
	 * @return string
	 */
	public function t($module, $key)
	{
		if (empty($this->cache)) {
			$this->cache = $this->getLanguageCache();
		}

		return isset($this->cache[$module][$key]) ? $this->cache[$module][$key] : strtoupper('{' . $module . '_' . $key . '}');
	}

	/**
	 * Überprüft, ob das angegebene Sprachpaket existiert
	 *
	 * @param string $lang
	 * @return boolean
	 */
	public static function languagePackExists($lang)
	{
		return !preg_match('=/=', $lang) && is_file(ACP3_ROOT . 'languages/' . $lang . '/info.xml') === true;
	}

	/**
	 * Parst den ACCEPT-LANGUAGE Header des Browsers
	 * und selektiert die präferierte Sprache
	 * 
	 * @return string
	 */
	final public static function parseAcceptLanguage()
	{
		$langs = array();

		if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			$matches = array();
			preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches);

			if (!empty($matches[1])) {
				$langs = array_combine($matches[1], $matches[4]);

				// Für Einträge ohne q-Faktor, Wert auf 1 setzen
				foreach ($langs as $lang => $val) {
					if ($val === '')
						$langs[$lang] = 1;
				}

				// Liste nach Sprachpräferenz sortieren
				arsort($langs, SORT_NUMERIC);
			}
		}

		// Über die Sprachen iterieren und das passende Sprachpaket auswählen
		foreach ($langs as $lang => $val) {
			if (ACP3_Lang::languagePackExists($lang) === true) {
				return $lang;
			}
		}
		return 'en';
	}
}