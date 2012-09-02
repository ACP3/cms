<?php
/**
 * Language
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Core
 */

if (defined('IN_ACP3') === false)
	exit;

/**
 * Stellt Funktionen bereit, um das ACP3 in verschiendene Sprachen zu übersetzen
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
	public function getLang()
	{
		return $this->lang;
	}

	/**
	 * Cached die Sprachfiles, um diese schneller verarbeiten zu können
	 */
	public function setLangCache()
	{
		$data = array();
		$path = ACP3_ROOT . 'languages/' . $this->lang . '/';
		$dir = scandir($path);
		foreach ($dir as $row) {
			$module = substr($row, 0, strrpos($row, '.'));
			if (is_file($path . $module . '.xml') === true) {
				$xml = simplexml_load_file($path . $module . '.xml');
				// Über die einzelnen Sprachstrings iterieren
				foreach ($xml->item as $item) {
					$data[$module][(string) $item['key']] = (string) $item;
				}
			}
		}
		ACP3_Cache::create('language_' . $this->lang, $data);
	}

	/**
	 * Gibt die gecacheten Sprachstrings aus
	 *
	 * @return array
	 */
	private function getLangCache()
	{
		$filename = 'language_' . $this->lang;
		if (ACP3_Cache::check($filename) === false)
			$this->setLangCache();

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
		static $lang_data = array();

		if (empty($lang_data)) {
			$lang_data = $this->getLangCache();
		}

		return isset($lang_data[$module][$key]) ? $lang_data[$module][$key] : strtoupper('{' . $module . '_' . $key . '}');
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
			preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches);

			if (count($matches[1])) {
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