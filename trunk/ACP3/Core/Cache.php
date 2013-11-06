<?php
namespace ACP3\Core;

/**
 * Klasse zur Erstellung des Caches, um die Leistung von bestimmten Aktionen des ACP3 zu steigern
 *
 * @author Tino Goratsch
 */
abstract class Cache
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
        $cache_id .= $cache_id !== '' ? '_' : '';
        return is_file(ACP3_ROOT_DIR . self::$sql_cache_dir . $cache_id . md5($filename) . '.php');
    }

    /**
     * Erstellt den Cache
     *
     * @param string $filename
     *    Gewünschter Dateiname des Caches
     * @param array $data
     *    Daten, für welche der Cache erstellt werden sollen
     * @return boolean
     */
    public static function create($filename, $data, $cache_id = '')
    {
        if (!empty($data)) {
            $cache_id .= $cache_id !== '' ? '_' : '';
            $bool = @file_put_contents(ACP3_ROOT_DIR . self::$sql_cache_dir . $cache_id . md5($filename) . '.php', serialize($data), LOCK_EX);

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
     *    Zu löschende Datei
     * @return boolean
     */
    public static function delete($filename, $cache_id = '')
    {
        $cache_id .= $cache_id !== '' ? '_' : '';
        return @unlink(ACP3_ROOT_DIR . self::$sql_cache_dir . $cache_id . md5($filename) . '.php');
    }

    /**
     * Ausgabe der gecacheten Aktion
     *
     * @param string $filename
     *    Auszugebende Datei
     * @return mixed
     */
    public static function output($filename, $cache_id = '')
    {
        $cache_id .= $cache_id !== '' ? '_' : '';
        $data = @file_get_contents(ACP3_ROOT_DIR . self::$sql_cache_dir . $cache_id . md5($filename) . '.php');
        return $data === false ? array() : unserialize($data);
    }

    /**
     * Löscht den gesamten Cache
     *
     * @param string $dir
     *    Einen Unterordner des Cache-Ordners löschen
     */
    public static function purge($dir = '', $cache_id = '')
    {
        $path = ACP3_ROOT_DIR . self::$cache_dir . (!empty($dir) && !preg_match('=/=', $dir) ? $dir . '/' : 'sql/');
        if (is_dir($path)) {
            $cache_id .= $cache_id !== '' ? '_' : '';

            $cache_dir = scandir($path);
            foreach ($cache_dir as $row) {
                if (is_file($path . $row) && $row !== '.htaccess') {
                    // Wenn eine $cache_id gesetzt wurde, nur diese Dateien löschen
                    if ($cache_id !== '' && strpos($row, $cache_id) !== 0)
                        continue;
                    @unlink($path . $row);
                }
            }
        }
        return;
    }
}