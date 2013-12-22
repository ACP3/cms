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
    protected static $cacheDir = 'uploads/cache/';
    /**
     *
     * @var string
     */
    protected static $sqlCacheDir = 'uploads/cache/sql/';

    /**
     * Überprüft, ob der Cache für eine bestimmte Abfrage schon erstellt wurde
     *
     * @param string $filename
     *  Der Name der Datei, welcher auf Existenz und Gültigkeit geprüft werden soll
     * @return boolean
     */
    public static function check($filename, $cacheId = '')
    {
        $cacheId .= $cacheId !== '' ? '_' : '';
        return is_file(ACP3_ROOT_DIR . self::$sqlCacheDir . $cacheId . md5($filename) . '.php');
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
    public static function create($filename, $data, $cacheId = '')
    {
        if (!empty($data)) {
            $cacheId .= $cacheId !== '' ? '_' : '';
            $bool = @file_put_contents(ACP3_ROOT_DIR . self::$sqlCacheDir . $cacheId . md5($filename) . '.php', serialize($data), LOCK_EX);

            return $bool !== false ? true : false;
        } elseif (self::check($filename, $cacheId) === true) {
            return self::delete($filename, $cacheId);
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
    public static function delete($filename, $cacheId = '')
    {
        $cacheId .= $cacheId !== '' ? '_' : '';
        return @unlink(ACP3_ROOT_DIR . self::$sqlCacheDir . $cacheId . md5($filename) . '.php');
    }

    /**
     * Ausgabe der gecacheten Aktion
     *
     * @param string $filename
     *    Auszugebende Datei
     * @return mixed
     */
    public static function output($filename, $cacheId = '')
    {
        $cacheId .= $cacheId !== '' ? '_' : '';
        $data = @file_get_contents(ACP3_ROOT_DIR . self::$sqlCacheDir . $cacheId . md5($filename) . '.php');
        return $data === false ? array() : unserialize($data);
    }

    /**
     * Löscht den gesamten Cache
     *
     * @param string $dir
     *    Einen Unterordner des Cache-Ordners löschen
     */
    public static function purge($dir = '', $cacheId = '')
    {
        $path = ACP3_ROOT_DIR . self::$cacheDir . (!empty($dir) && !preg_match('=/=', $dir) ? $dir . '/' : 'sql/');
        if (is_dir($path)) {
            $cacheId .= $cacheId !== '' ? '_' : '';

            $cacheDir = scandir($path);
            foreach ($cacheDir as $row) {
                if (is_file($path . $row) && $row !== '.htaccess') {
                    // Wenn eine $cache_id gesetzt wurde, nur diese Dateien löschen
                    if ($cacheId !== '' && strpos($row, $cacheId) !== 0)
                        continue;
                    @unlink($path . $row);
                }
            }
        }
        return;
    }
}