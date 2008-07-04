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
	private $lang = CONFIG_LANG;

	function __construct()
	{
		global $auth;

		// Installer abfangen
		if (isset($auth)) {
			$info = $auth->getUserInfo();
			if (!empty($info)) {
				$this->lang = $info['language'];
			}
		} else {
			$this->lang = LANG;
		}
	}
	/**
	 * Cached die Sprachfiles, um diese schneller verarbeiten zu können
	 */
	public function createLangCache()
	{
		$data = array();
		$path = ACP3_ROOT . 'languages/' . $this->lang . '/';
		$dir = scandir($path);
		foreach ($dir as $row) {
			$module = substr($row, 0, strrpos($row, '.'));
			if (is_file($path . $module . '.xml')) {
				$xml = simplexml_load_file($path . $module . '.xml');
				foreach ($xml->item as $row) {
					$data[$module][(string) $row->name] = (string) $row->message;
				}
			}
		}
		cache::create('language_' . $this->lang, $data);
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
			$lang_data = $this->outputLangCache();
		}
		$path = ACP3_ROOT . 'languages/' . $this->lang . '/' . $module . '.xml';

		return isset($lang_data[$module][$key]) ? $lang_data[$module][$key] : strtoupper('{' . $module . '_' . $key . '}');
	}
	/**
	 * Gibt die gecacheten Sprachstrings aus
	 *
	 * @return array
	 */
	private function outputLangCache()
	{
		$filename = 'language_' . $this->lang;
		if (!cache::check($filename)) {
			$this->createLangCache();
		}
		return cache::output($filename);
	}
}
?>