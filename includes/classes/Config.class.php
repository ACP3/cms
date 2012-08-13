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
	 * Erstellt/Verändert die Hauptkonfigurationsdatei des ACP3
	 *
	 * @param array $data
	 * 	Zu schreibende Daten
	 * @return boolean
	 */
	public static function system(array $data)
	{
		$path = INCLUDES_DIR . 'config.php';
		if (is_writable($path) === true){
			// Konfigurationsdatei in ein Array schreiben
			$config = array(
				'cache_images' => CONFIG_CACHE_IMAGES,
				'cache_minify' => CONFIG_CACHE_MINIFY,
				'date_format_long' => CONFIG_DATE_FORMAT_LONG,
				'date_format_short' => CONFIG_DATE_FORMAT_SHORT,
				'date_time_zone' => CONFIG_DATE_TIME_ZONE,
				'db_host' => CONFIG_DB_HOST,
				'db_name' => CONFIG_DB_NAME,
				'db_password' => CONFIG_DB_PASSWORD,
				'db_pre' => CONFIG_DB_PRE,
				'db_user' => CONFIG_DB_USER,
				'db_version' => CONFIG_DB_VERSION,
				'design' => CONFIG_DESIGN,
				'entries' => CONFIG_ENTRIES,
				'flood' => CONFIG_FLOOD,
				'homepage' => CONFIG_HOMEPAGE,
				'lang' => CONFIG_LANG,
				'mailer_smtp_auth' => CONFIG_MAILER_SMTP_AUTH,
				'mailer_smtp_host' => CONFIG_MAILER_SMTP_HOST,
				'mailer_smtp_password' => CONFIG_MAILER_SMTP_HOST,
				'mailer_smtp_port' => CONFIG_MAILER_SMTP_PORT,
				'mailer_smtp_security' => CONFIG_MAILER_SMTP_SECURITY,
				'mailer_smtp_user' => CONFIG_MAILER_SMTP_HOST,
				'mailer_type' => CONFIG_MAILER_TYPE,
				'maintenance_message' => CONFIG_MAINTENANCE_MESSAGE,
				'maintenance_mode' => CONFIG_MAINTENANCE_MODE,
				'seo_aliases' => CONFIG_SEO_ALIASES,
				'seo_meta_description' => CONFIG_SEO_META_DESCRIPTION,
				'seo_meta_keywords' => CONFIG_SEO_META_KEYWORDS,
				'seo_mod_rewrite' => CONFIG_SEO_MOD_REWRITE,
				'seo_robots' => CONFIG_SEO_ROBOTS,
				'seo_title' => CONFIG_SEO_TITLE,
				'version' => CONFIG_VERSION,
				'wysiwyg' => CONFIG_WYSIWYG
			);
			$data = array_merge($config, $data);

			ksort($data);

			$content = "<?php\n";
			$content.= "define('INSTALLED', true);\n";
			if (defined('DEBUG') === true)
				$content.= "define('DEBUG', " . ((bool) DEBUG === true ? 'true' : 'false') . ");\n";
			$pattern = "define('CONFIG_%s', %s);\n";
			foreach ($data as $key => $value) {
				if (array_key_exists($key, $config) === true)
					if ($value !== '' && is_numeric($value) === true)
						$value = $value;
					elseif (is_bool($value) === true)
						$value = $value === true ? 'true' : 'false';
					else
						$value = '\'' . $value . '\'';
					$content.= sprintf($pattern, strtoupper($key), $value);
			}
			$bool = @file_put_contents($path, $content, LOCK_EX);
			return $bool !== false ? true : false;
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
		global $db;

		$bool = false;
		foreach ($data as $key => $value) {
			$bool = $db->update('settings', array('value' => $value), 'module = \'' . $db->escape($module) . '\' AND name = \'' . $key . '\'');
		}
		$bool2 = self::setModuleCache($module);

		return $bool !== false && $bool2 !== false ? true : false;
	}
	/**
	 * Gibt den Inhalt der Konfigurationsdateien der Module aus
	 *
	 * @param string $module
	 * @return array
	 */
	public static function getModuleSettings($module)
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

		$settings = $db->select('name, value', 'settings', 'module = \'' . $db->escape($module) . '\'');
		$c_settings = count($settings);

		$cache_ary = array();
		for ($i = 0; $i < $c_settings; ++$i) {
			$cache_ary[$settings[$i]['name']] = $settings[$i]['value'];
		}

		return ACP3_Cache::create($module . '_settings', $cache_ary);
	}
}