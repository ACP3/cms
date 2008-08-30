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
	/**
	 * Dateinamenserweiterung für die gecacheten Dateien
	 *
	 * @var string
	 * @access private
	 */
	private static $ext = '.cache';

	/**
	 * Überprüft, ob der Cache für eine bestimmte Abfrage schon erstellt wurde
	 *
	 * @param string $filename
	 * @return boolean
	 */
	public static function check($filename)
	{
		// Datei alle 15 Minuten neu cachen
		$eol = 900;
		$path = ACP3_ROOT . 'cache/' . md5($filename) . self::$ext;
		if (is_file($path) && time() - filemtime($path) <  $eol) {
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
	 * 	Daten, welche gecachet werden sollen
	 * @return boolean
	 */
	public static function create($filename, $data)
	{
		if (!empty($data)) {
			$bool = @file_put_contents(ACP3_ROOT . 'cache/' . md5($filename) . self::$ext, serialize($data));

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
		$path = ACP3_ROOT . 'cache/' . md5($filename) . self::$ext;
		if (is_file($path)) {
			return unlink($path);
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
			return unserialize(@file_get_contents(ACP3_ROOT . 'cache/' . md5($filename) . self::$ext));
		}
		return array();
	}
	/**
	 * Löscht en gesamten Cache
	 */
	public static function purge()
	{
		$cache_dir = scandir(ACP3_ROOT . 'cache');
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