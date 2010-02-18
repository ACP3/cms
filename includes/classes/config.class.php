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
	public static function system($data)
	{
		$path = ACP3_ROOT . 'includes/config.php';
		if (is_writable($path) && is_array($data)){
			ksort($data);

			// Konfigurationsdatei in ein Array schreiben
			$content = "<?php\n";
			$content.= "define('INSTALLED', true);\n";
			if (defined('DEBUG')) {
				$content.= "define('DEBUG', " . ((bool) DEBUG) . ");\n"; 
			}
			$pattern = "define('CONFIG_%s', '%s');\n";
			foreach ($data as $key => $value) {
				$content.= sprintf($pattern, strtoupper($key), $value);
			}
			$content.= '?>';
			$bool = @file_put_contents($path, $content);
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
	public static function module($module, $data)
	{
		return xml::writeToXml(ACP3_ROOT . 'modules/' . $module . '/module.xml', 'settings/*', $data);
	}
	/**
	 * Gibt den Inhalt der Konfigurationsdateien der Module aus
	 *
	 * @param string $module
	 * @return mixed
	 */
	public static function output($module)
	{
		return xml::parseXmlFile(ACP3_ROOT . 'modules/' . $module . '/module.xml', 'settings');
	}
}