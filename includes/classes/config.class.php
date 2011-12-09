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
		$path = INCLUDES_DIR . 'config.php';
		if (is_writable($path) && is_array($data)){
			// Konfigurationsdatei in ein Array schreiben
			$config = array(
				'date_dst' => CONFIG_DATE_DST,
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
				'maintenance_message' => CONFIG_MAINTENANCE_MESSAGE,
				'maintenance_mode' => CONFIG_MAINTENANCE_MODE,
				'seo_meta_description' => CONFIG_SEO_META_DESCRIPTION,
				'seo_meta_keywords' => CONFIG_SEO_META_KEYWORDS,
				'seo_mod_rewrite' => CONFIG_SEO_MOD_REWRITE,
				'seo_title' => CONFIG_SEO_TITLE,
				'version' => CONFIG_VERSION,
				'wysiwyg' => CONFIG_WYSIWYG
			);
			$data = array_merge($config, $data);

			ksort($data);

			$content = "<?php\n";
			$content.= "define('INSTALLED', true);\n";
			if (defined('DEBUG')) {
				$content.= "define('DEBUG', " . ((bool) DEBUG) . ");\n";
			}
			$pattern = "define('CONFIG_%s', '%s');\n";
			foreach ($data as $key => $value) {
				if (array_key_exists($key, $config))
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
		return xml::writeToXml(MODULES_DIR . '' . $module . '/module.xml', 'settings/*', $data);
	}
	/**
	 * Gibt den Inhalt der Konfigurationsdateien der Module aus
	 *
	 * @param string $module
	 * @return mixed
	 */
	public static function output($module)
	{
		return xml::parseXmlFile(MODULES_DIR . '' . $module . '/module.xml', 'settings');
	}
}