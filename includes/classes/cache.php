<?php
/**
 * Cache
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */
/**
 * Erstellt den Cache für bestimmte SQL Abfragen, um die Leistung des ACP3 zu steigern
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */
class cache
{
	/**
	 * Überprüft, ob der SQL Cache für eine bestimmte Abfrage schon erstellt wurde
	 *
	 * @param string $filename
	 * @return boolean
	 */
	public static function check($filename)
	{
		if (is_file(ACP3_ROOT . 'cache/sql_' . md5($filename) . '.php')) {
			return true;
		}
		return false;
	}
	/**
	 * Erstellt den SQL Cache
	 *
	 * @param string $filename
	 * 	Gewünschter Dateiname des SQL Caches
	 * @param array $sql_results
	 * 	Datensätze der SQL Abfrage
	 * @return boolean
	 */
	public static function create($filename, $sql_results)
	{
		if (!empty($sql_results)) {
			$bool = @file_put_contents(ACP3_ROOT . 'cache/sql_' . md5($filename) . '.php', serialize($sql_results));

			return $bool ? true : false;
		} elseif (self::check($filename)) {
			return self::delete($filename);
		}
		return false;
	}
	/**
	 * Löscht den SQL Cache für einen bestimmten SQL Cache
	 *
	 * @param string $filename
	 * 	Zu löschende Datei
	 * @return boolean
	 */
	public static function delete($filename)
	{
		if (self::check($filename)) {
			return unlink(ACP3_ROOT . 'cache/sql_' . md5($filename) . '.php');
		}
		return false;
	}
	/**
	 * Ausgabe des SQL Caches
	 *
	 * @param string $filename
	 * 	Auszugebende Datei
	 * @return mixed
	 */
	public static function output($filename)
	{
		if (self::check($filename)) {
			return unserialize(@file_get_contents(ACP3_ROOT . 'cache/sql_' . md5($filename) . '.php'));
		}
		return array();
	}
	/**
	 * Löscht alle gecacheten SQL Queries
	 */
	public static function purge()
	{
		$cache_dir = scandir('cache');
		$c_cache_dir = count($cache_dir);

		for ($i = 0; $i < $c_cache_dir; ++$i) {
			if (is_file(ACP3_ROOT . 'cache/' . $cache_dir[$i]) && $cache_dir[$i] != '.htaccess') {
				unlink(ACP3_ROOT . 'cache/' . $cache_dir[$i]);
			}
		}
		return;
	}
}
?>