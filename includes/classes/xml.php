<?php
/**
 * XML Parser
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */
/**
 * Parst XML Dateien, z.B. die diversen info.xml bzw. module.xml Dateien
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */
class xml
{
	/**
	 * Parst die angeforderte XML Datei
	 *
	 * @param string $path
	 * @param string $xpath
	 * @return mixed
	 */
	public static function parseXmlFile($path, $xpath)
	{
		static $info = array();

		if (!empty($info[$path][$xpath])) {
			return $info[$path][$xpath];
		} elseif (is_file($path)) {
			$xml = simplexml_load_file($path);
			$data = $xml->xpath($xpath);

			foreach ($data as $row) {
				foreach ($row as $key => $value) {
					if ($value->attributes()) {
						foreach ($value->attributes() as $attr_key => $attr_val) {
							if ($key == 'version' && $attr_key == 'core' && $attr_val == 'true') {
								$info[$path][$xpath]['version'] = CONFIG_VERSION;
							} else {
								$info[$path][$xpath][(string) $key][(string) $attr_key] = (string) $attr_val;
							}
						}
					} else {
						$info[$path][$xpath][(string) $key] = (string) $value;
					}
				}
			}
			return $info[$path][$xpath];
		}
		return false;
	}
	/**
	 * Schreibt Änderungen in die angegebene XML Datei
	 *
	 * @param string $path
	 * @param string $xpath
	 * @param array $data
	 * @return boolean
	 */
	public static function writeToXml($path, $xpath, $data)
	{
		if (is_file($path) && is_writable($path) && is_array($data)) {
			$xml = new DOMDocument();
			$xml->load($path);
			$xp = new domxpath($xml);
			$items = $xp->query($xpath);
			$i = $items->length - 1;

			while ($i > -1) {
				$item = $items->item($i);

				if (array_key_exists($item->nodeName, $data)) {
					$newitem = $xml->createElement($item->nodeName);
					if (empty($data[$item->nodeName]) || validate::isNumber($data[$item->nodeName])) {
						$newitem_content = $xml->createTextNode($data[$item->nodeName]);
					} else {
						$newitem_content = $xml->createCDATASection($data[$item->nodeName]);	
					}
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
}