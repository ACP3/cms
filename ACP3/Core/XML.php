<?php
namespace ACP3\Core;

/**
 * Parst XML Dateien, z.B. die diversen info.xml bzw. module.xml Dateien
 *
 * @author Tino Goratsch
 */
abstract class XML {

	/**
	 * Cache für bereits ausgelesene XML-Dateien
	 * 
	 * @var array
	 */
	private static $info = array();

	/**
	 * Parst die angeforderte XML Datei
	 *
	 * @param string $path
	 * @param string $xpath
	 * @return mixed
	 */
	public static function parseXmlFile($path, $xpath) {
		if (!empty(self::$info[$path][$xpath])) {
			return self::$info[$path][$xpath];
		} elseif (is_file($path) === true) {
			$xml = simplexml_load_file($path);
			$data = $xml->xpath($xpath);

			if (!empty($data)) {
				foreach ($data as $row) {
					foreach ($row as $key => $value) {
						if ($value->attributes()) {
							foreach ($value->attributes() as $attr_key => $attr_val) {
								if ($key === 'version' && $attr_key === 'core' && (string) $attr_val === 'true') {
									self::$info[$path][$xpath]['version'] = CONFIG_VERSION;
								} else {
									self::$info[$path][$xpath][(string) $key][(string) $attr_key] = (string) $attr_val;
								}
							}
						} elseif (isset(self::$info[$path][$xpath][(string) $key]) && is_array(self::$info[$path][$xpath][(string) $key])) {
							self::$info[$path][$xpath][(string) $key][] = (string) $value;
						} elseif (isset(self::$info[$path][$xpath][(string) $key])) {
							$tmp = self::$info[$path][$xpath][(string) $key];
							self::$info[$path][$xpath][(string) $key] = array();
							self::$info[$path][$xpath][(string) $key][] = $tmp;
							self::$info[$path][$xpath][(string) $key][] = (string) $value;
						} else {
							self::$info[$path][$xpath][(string) $key] = (string) $value;
						}
					}
				}
				return self::$info[$path][$xpath];
			}
		}
		return array();
	}

	/**
	 * Schreibt Änderungen in die angegebene XML Datei
	 *
	 * @param string $path
	 * @param string $xpath
	 * @param array $data
	 * @return boolean
	 */
	public static function writeToXml($path, $xpath, $data) {
		if (is_file($path) === true && is_writable($path) === true && is_array($data) === true) {
			$xml = new DOMDocument();
			$xml->load($path);
			$xp = new domxpath($xml);
			$items = $xp->query($xpath);
			$i = $items->length - 1;

			while ($i > -1) {
				$item = $items->item($i);

				if (array_key_exists($item->nodeName, $data) === true) {
					$newitem = $xml->createElement($item->nodeName);
					if (empty($data[$item->nodeName]) ||
							Validate::isNumber($data[$item->nodeName]) ||
							Validate::email($data[$item->nodeName]) ||
							preg_match('/^(\w+)$/', $data[$item->nodeName])) {
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