<?php
/**
 * Config
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Core
 */

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
		$settings = self::getSettings('system');
		foreach ($settings as $key => $value) {
			define('CONFIG_' . strtoupper($key), $value);
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
		$bool = $bool2 = false;
		$mod_id = ACP3_CMS::$db2->fetchColumn('SELECT id FROM ' . DB_PRE . 'modules WHERE name = ?', array($module));
		if (!empty($mod_id)) {
			foreach ($data as $key => $value) {
				$bool = ACP3_CMS::$db2->executeUpdate('UPDATE ' . DB_PRE . 'settings SET value = ? WHERE module_id = ? AND name = ?', array($value, (int) $mod_id, $key));
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
		$settings = ACP3_CMS::$db2->executeQuery('SELECT s.name, s.value FROM ' . DB_PRE . 'settings AS s JOIN ' . DB_PRE . 'modules AS m ON(m.id = s.module_id) WHERE m.name = ?', array($module))->fetchAll();
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