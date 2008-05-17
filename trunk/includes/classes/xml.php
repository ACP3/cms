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
	public static function parseXmlFile($path, $level = '')
	{
		static $info = array();

		if (!empty($info[$path . $level])) {
			return $info[$path . $level];
		} elseif (is_file($path)) {
			$xml = simplexml_load_file($path);
			$data = !empty($level) ? $xml->xpath($level) : $xml;

			foreach ($data as $row) {
				foreach ($row as $key => $value) {
					if ($value->attributes()) {
						foreach ($value->attributes() as $attr_key => $attr_val) {
							$info[$path . $level][(string) $key][(string) $attr_key] = (string) $attr_val;
						}
					} else {
						$info[$path . $level][(string) $key] = (string) $value;
					}
				}
			}
			return $info[$path . $level];
		}
		return false;
	}
}
?>