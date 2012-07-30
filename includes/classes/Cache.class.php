<?php
/**
 * Cache
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Core
 */

if (defined('IN_ACP3') === false)
	exit;

/**
 * lasse zur Ersetllung des Caches, um die Leistung von bestimmten Aktionen des ACP3 zu steigern
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Core
 */
class ACP3_Cache
{
	/**
	 *
	 * @var string 
	 */
	private static $cache_dir = 'uploads/cache/';
	/**
	 *
	 * @var string 
	 */
	private static $sql_cache_dir = 'uploads/cache/sql/';

	/**
	 * Überprüft, ob der Cache für eine bestimmte Abfrage schon erstellt wurde
	 *
	 * @param string $filename
	 *  Der Name der Datei, welcher auf Existenz und Gültigkeit geprüft werden soll
	 * @return boolean
	 */
	public static function check($filename, $cache_id = '')
	{
		$cache_id.= $cache_id !== '' ? '_' : '';
		return is_file(ACP3_ROOT . self::$sql_cache_dir . $cache_id . md5($filename) . '.php');
	}
	/**
	 * Erstellt den Cache
	 *
	 * @param string $filename
	 * 	Gewünschter Dateiname des Caches
	 * @param array $data
	 * 	Daten, für welche der Cache erstellt werden sollen
	 * @return boolean
	 */
	public static function create($filename, $data, $cache_id = '')
	{
		if (!empty($data)) {
			$cache_id.= $cache_id !== '' ? '_' : '';
			$bool = @file_put_contents(ACP3_ROOT . self::$sql_cache_dir . $cache_id . md5($filename) . '.php', serialize($data), LOCK_EX);

			return $bool !== false ? true : false;
		} elseif (self::check($filename, $cache_id) === true) {
			return self::delete($filename, $cache_id);
		}
		return false;
	}
	/**
	 * Löscht eine bestimmte gecachete Datei
	 *
	 * @param string $filename
	 * 	Zu löschende Datei
	 * @return boolean
	 */
	public static function delete($filename, $cache_id = '')
	{
		$cache_id.= $cache_id !== '' ? '_' : '';
		return @unlink(ACP3_ROOT . self::$sql_cache_dir . $cache_id . md5($filename) . '.php');
	}
	/**
	 * Ausgabe der gecacheten Aktion
	 *
	 * @param string $filename
	 * 	Auszugebende Datei
	 * @return mixed
	 */
	public static function output($filename, $cache_id = '')
	{
		$cache_id.= $cache_id !== '' ? '_' : '';
		$data = @file_get_contents(ACP3_ROOT . self::$sql_cache_dir . $cache_id . md5($filename) . '.php');
		return $data === false ? array() : unserialize($data);
	}
	/**
	 * Löscht den gesamten Cache
	 *
	 * @param string $dir
	 *	Einen Unterordner des Cache-Ordners löschen
	 */
	public static function purge($dir = 'sql', $cache_id = '')
	{
		$path = ACP3_ROOT . self::$cache_dir . (!empty($dir) && !preg_match('=/=', $dir) ? $dir . '/' : '');
		if (is_dir($path)) {
			$cache_id.= $cache_id !== '' ? '_' : '';
			$cache_dir = scandir($path);
			$c_cache_dir = count($cache_dir);

			for ($i = 0; $i < $c_cache_dir; ++$i) {
				if (is_file($path . $cache_dir[$i]) && $cache_dir[$i] !== '.htaccess') {
					// Wenn eine $cache_id gesetzt wurde, nur diese Dateien löschen
					if ($cache_id !== '' && preg_match('/^(' . $cache_id . ')/', $cache_dir[$i]) === true)
						continue;
					unlink($path . $cache_dir[$i]);
				}
			}
		}
		return;
	}
}