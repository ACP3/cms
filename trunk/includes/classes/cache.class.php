<?php
/**
 * Cache
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */
/**
 * lasse zur Ersetllung des Caches, um die Leistung von bestimmten Aktionen des ACP3 zu steigern
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */
class cache
{
	private static $cache_dir = 'cache/sql/';

	/**
	 * Überprüft, ob der Cache für eine bestimmte Abfrage schon erstellt wurde
	 *
	 * @param string $filename
	 *  Der Name der Datei, welcher auf Existenz und Gültigkeit geprüft werden soll
	 * @return boolean
	 */
	public static function check($filename)
	{
		if (is_file(ACP3_ROOT . self::$cache_dir . 'cache_' . md5($filename) . '.php')) {
			return true;
		}
		return false;
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
	public static function create($filename, $data)
	{
		if (!empty($data)) {
			$bool = @file_put_contents(ACP3_ROOT . self::$cache_dir . 'cache_' . md5($filename) . '.php', serialize($data), LOCK_EX);

			return $bool ? true : false;
		} elseif (self::check($filename)) {
			return self::delete($filename);
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
	public static function delete($filename)
	{
		if (self::check($filename)) {
			return unlink(ACP3_ROOT . self::$cache_dir . 'cache_' . md5($filename) . '.php');
		}
		return false;
	}
	/**
	 * Ausgabe der gecacheten Aktion
	 *
	 * @param string $filename
	 * 	Auszugebende Datei
	 * @return mixed
	 */
	public static function output($filename)
	{
		if (self::check($filename)) {
			$handle = fopen(ACP3_ROOT . self::$cache_dir . 'cache_' . md5($filename) . '.php', 'r');
			flock($handle, LOCK_SH);
			$data = unserialize(stream_get_contents($handle));
			flock($handle, LOCK_UN); // Release the lock
			fclose($handle);
			return $data;
		}
		return array();
	}
	/**
	 * Löscht den gesamten Cache
	 *
	 * @param string $dir
	 *	Einen Unterordner des Cache-Ordners löschen
	 */
	public static function purge($dir = 0, $delete_folder = 0)
	{
		$path = ACP3_ROOT . self::$cache_dir . (!empty($dir) && !preg_match('=/=', $dir) ? $dir . '/' : '');
		if (is_dir($path)) {
			$cache_dir = scandir($path);
			$c_cache_dir = count($cache_dir);

			for ($i = 0; $i < $c_cache_dir; ++$i) {
				if (is_file($path . $cache_dir[$i]) && $cache_dir[$i] != '.htaccess') {
					unlink($path . $cache_dir[$i]);
				}
			}
			// Falls angewählt, den Unterordner auch löschen
			if (!empty($dir) && $delete_folder == 1) {
				rmdir($path);
			}
		}
		return;
	}
}