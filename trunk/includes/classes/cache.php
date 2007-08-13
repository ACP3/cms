<?php
/**
 * Erstellt den Cache für bestimmte SQL Abfragen um die Leistung des ACP3 zu steigern
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
	function check($filename)
	{
		if (is_file('cache/sql_' . md5($filename) . '.php')) {
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
	function create($filename, $sql_results)
	{
		if (!empty($sql_results)) {
			$content = '<?php' . "\n";
			$content.= 'if (!defined(\'IN_ACP3\') && !defined(\'IN_ADM\'))' . "\n";
			$content.= "\t" . 'exit;' . "\n\n";
			$content.= '$results = ' . var_export($sql_results, true) . ';' . "\n";
			$content.= '?>';

			$bool = @file_put_contents('cache/sql_' . md5($filename) . '.php', $content);

			return $bool ? true : false;
		} elseif ($this->check($filename)) {
			$this->delete($filename);

			return true;
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
	function delete($filename)
	{
		if ($this->check($filename)) {
			return unlink('cache/sql_' . md5($filename) . '.php');
		}
		return false;
	}
	/**
	 * Ausgabe des SQL Cache
	 *
	 * @param string $filename
	 * 	Auszugebende Datei
	 * @return mixed
	 */
	function output($filename)
	{
		if ($this->check($filename)) {
			$results = array();
			include 'cache/sql_' . md5($filename) . '.php';
			return $results;
		}
		return null;
	}
	/**
	 * Löscht alle gecacheten SQL Queries
	 */
	function purge()
	{
		$cache_dir = scandir('cache');
		$c_cache_dir = count($cache_dir);

		for ($i = 0; $i < $c_cache_dir; $i++) {
			if (is_file('cache/' . $cache_dir[$i]) && $cache_dir[$i] != '.htaccess') {
				unlink('cache/' . $cache_dir[$i]);
			}
		}
		return;
	}
}
?>