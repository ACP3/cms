<?php
/**
 * Language
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */
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

	function __construct()
	{
		global $auth;

		// Installer abfangen
		if (isset($auth)) {
			$lang = $auth->getUserLanguage();
			if (!preg_match('=/=', $lang) && is_dir(ACP3_ROOT . 'languages/' . $lang . '/'))
				$this->lang = $lang;
			else
				$this->lang = CONFIG_LANG;
		} else {
			$this->lang = LANG;
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
			if (is_file($path . $module . '.xml')) {
				$xml = simplexml_load_file($path . $module . '.xml');
				foreach ($xml->item as $row) {
					$data[$module][(string) $row['key']] = (string) $row;
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
		if (!cache::check($filename))
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
}