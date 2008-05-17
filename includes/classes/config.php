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
	public static function general($data)
	{
		$path = ACP3_ROOT . 'includes/config.php';
		if (is_writable($path) && is_array($data)){
			$data = ksort($data);

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
		$path = ACP3_ROOT . 'modules/' . $module . '/module.xml';
		if (!preg_match('=/=', $module) && is_file($path) && is_writable($path)) {
			$xml = new DOMDocument();
			$xml->load($path);
			$xp = new domxpath($xml);
			$items = $xp->query('settings/*');
			$i = $items->length - 1;

			while ($i > -1) {
				$item = $items->item($i);

				if (array_key_exists($item->nodeName, $data)) {
					$newitem = $xml->createElement($item->nodeName);
					$newitem_content = $xml->createCDATASection($data[$item->nodeName]);
					$newitem->appendChild($newitem_content);
					$item->parentNode->replaceChild($newitem, $item);
				}
				$i--;
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
	public static function output($module)
	{
		static $settings = array();

		if (!array_key_exists($module, $settings)) {
			$path = ACP3_ROOT . 'modules/' . $module . '/module.xml';
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