<?php
/**
 * Language
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */

if (defined('IN_ACP3') === false)
	exit;

/**
 * Stellt Funktionen bereit, um das ACP3 in verschiendene Sprachen zu übersetzen
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */
class lang
{
	/**
	 * Die zur Zeit eingestellte Sprache
	 *
	 * @var string
	 * @access private
	 */
	private $lang = '';

	function __construct($lang = '')
	{
		// Installer abfangen
		if (empty($lang)) {
			global $auth;

			$lang = $auth->getUserLanguage();
			$this->lang = $this->languagePackExists($lang) === true ? $lang : CONFIG_LANG;
		} else {
			$this->lang = $lang;
		}
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
		cache::create('language_' . $this->lang, $data);
	}
	/**
	 * Gibt die gecacheten Sprachstrings aus
	 *
	 * @return array
	 */
	private function getLangCache()
	{
		$filename = 'language_' . $this->lang;
		if (cache::check($filename) === false)
			$this->setLangCache();

		return cache::output($filename);
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
	public function languagePackExists($lang)
	{
		return !preg_match('=/=', $lang) && is_file(ACP3_ROOT . 'languages/' . $lang . '/info.xml') === true;
	}
}