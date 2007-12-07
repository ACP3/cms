<?php
/**
 * Config
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */
/**
 * Erstellt die jeweiligen Konfigurationsdateien für Module, etc.
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */
class config
{
	/**
	 * Erstellt/Verändert die Hauptkonfigurationsdatei des ACP3
	 *
	 * @param array $data
	 * 	Zu schreibende Daten
	 * @return boolean
	 */
	function general($data)
	{
		$path = 'includes/config.php';
		if (is_writable($path))	{
			// Konfigurationsdatei in ein Array schreiben
			$config = file($path);
			foreach ($data as $key => $value) {
				$old_entry = 'define(\'CONFIG_' . strtoupper($key) . '\', \'' . constant('CONFIG_' . strtoupper($key)) . '\');' . "\n";
				$new_entry = 'define(\'CONFIG_' . strtoupper($key) . '\', \'' . $value . '\');' . "\n";
				foreach ($config as $c_key => $c_value) {
					if ($old_entry == $c_value && $new_entry != $c_value) {
						$config[$c_key] = $new_entry;
					}
				}
			}
			$bool = @file_put_contents($path, $config);
			return $bool ? true : false;
		}
		return false;
	}
	/**
	 * Erstellt/Verändert die Konfigurationsdateien für die Module
	 *
	 * @param string $module
	 * @param array $data
	 * @return boolean
	 */
	function module($module, $data)
	{
		$path = 'modules/' . $module . '/module.xml';
		if (!preg_match('=/=', $module) && is_file($path)) {
			$xml = DOMDocument::load($path);
			$xp = new domxpath($xml);
			$items = $xp->query('settings');

			foreach ($items as $item) {
				foreach ($item->childNodes as $cNode) {
					if ($cNode->nodeType == 1 && array_key_exists($cNode->nodeName, $data)) {
						$replace = $xml->createElement($cNode->nodeName);
						$replace_content = $xml->createCDATASection($data[$cNode->nodeName]);
						$replace->appendChild($replace_content);

						$cNode->parentNode->replaceChild($replace, $cNode);
					}
				}
			}
			$bool = $xml->save($path);

			return $bool ? true : false;
		}
		return false;
	}
	/**
	 * Gibt den Inhalt der Konfigurationsdateien der Module aus
	 *
	 * @param string $module
	 * @return mixed
	 */
	function output($module)
	{
		static $settings = array();

		if (!array_key_exists($module, $settings)) {
			$path = 'modules/' . $module . '/module.xml';
			if (!preg_match('=/=', $module) && is_file($path)) {
				$xml = simplexml_load_file($path);

				foreach ($xml->xpath('settings') as $row) {
					foreach ($row as $key => $value) {
						$settings[$module][$key] = $value;
					}
				}
				return $settings[$module];
			}
			return false;
		}
		return $settings[$module];
	}
}
?>