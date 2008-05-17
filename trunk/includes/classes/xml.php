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

		if (!empty($info[$path . '_' . $xpath])) {
			return $info[$path . '_' . $xpath];
		} elseif (is_file($path)) {
			$xml = simplexml_load_file($path);
			$data = $xml->xpath($xpath);

			foreach ($data as $row) {
				foreach ($row as $key => $value) {
					if ($value->attributes()) {
						foreach ($value->attributes() as $attr_key => $attr_val) {
							if ($key == 'version' && $attr_key == 'core' && $attr_val == 'true') {
								$info[$path . '_' . $xpath]['version'] = CONFIG_VERSION;
							} else {
								$info[$path . '_' . $xpath][(string) $key][(string) $attr_key] = (string) $attr_val;
							}
						}
					} else {
						$info[$path . '_' . $xpath][(string) $key] = (string) $value;
					}
				}
			}
			return $info[$path . '_' . $xpath];
		}
		return false;
	}
}
?>