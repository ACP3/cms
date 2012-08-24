<?php
/**
 * Config
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Core
 */

if (defined('IN_ACP3') === false)
	exit;

/**
 * Erstellt die jeweiligen Konfigurationsdateien für Module, etc.
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Core
 */
class ACP3_Config
{
	/**
	 * Gibt die Systemeinstellungen aus
	 */
	public static function getSystemSettings()
	{
		global $db;

		$settings = self::getSettings('system');
		foreach ($settings as $key => $value) {
			define('CONFIG_' . strtoupper($key), $db->escape($value, 3));
		}
		return;
	}
	/**
	 * Erstellt/Verändert die Konfigurationsdateien für die Module
	 *
	 * @param string $module
	 * @param array $data
	 * @return boolean
	 */
	public static function setSettings($module, $data)
	{
		global $db;

		$bool = $bool2 = false;
		$mod_id = $db->select('id', 'modules', 'name = \'' . $db->escape($module) . '\'');
		if (!empty($mod_id)) {
			foreach ($data as $key => $value) {
				$bool = $db->update('settings', array('value' => $value), 'module_id = ' . $mod_id[0]['id'] . ' AND name = \'' . $key . '\'');
			}
			$bool2 = self::setModuleCache($module);
		}

		return $bool !== false && $bool2 !== false ? true : false;
	}
	/**
	 * Gibt den Inhalt der Konfigurationsdateien der Module aus
	 *
	 * @param string $module
	 * @return array
	 */
	public static function getSettings($module)
	{
		if (ACP3_Cache::check($module . '_settings') === false)
			self::setModuleCache($module);

		return ACP3_Cache::output($module . '_settings');
	}
	/**
	 * Setzt den Cache für die Einstellungen eines Moduls
	 *
	 * @param string $module
	 * @return boolean
	 */
	private static function setModuleCache($module)
	{
		global $db;

		$settings = $db->query('SELECT s.name, s.value FROM {pre}settings AS s JOIN {pre}modules AS m ON(m.id = s.module_id) WHERE m.name = \'' . $db->escape($module) . '\'');
		$c_settings = count($settings);

		$cache_ary = array();
		for ($i = 0; $i < $c_settings; ++$i) {
			if (is_int($settings[$i]['value']))
				$cache_ary[$settings[$i]['name']] = (int) $settings[$i]['value'];
			elseif (is_float($settings[$i]['value']))
				$cache_ary[$settings[$i]['name']] = (float) $settings[$i]['value'];
			else
				$cache_ary[$settings[$i]['name']] = $settings[$i]['value'];
		}

		return ACP3_Cache::create($module . '_settings', $cache_ary);
	}
}