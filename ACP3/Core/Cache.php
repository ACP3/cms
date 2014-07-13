<?php
namespace ACP3\Core;

/**
 * Klasse zur Erstellung des Caches, um die Leistung von bestimmten Aktionen des ACP3 zu steigern
 *
 * @author Tino Goratsch
 */
class Cache
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
     * Löscht den gesamten Cache
     *
     * @param string $dir
     *    Einen Unterordner des Cache-Ordners löschen
     * @param string $cacheId
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
                    if ($cacheId !== '' && strpos($row, $cacheId) !== 0) {
                        continue;
                    }
                    @unlink($path . $row);
                }
            }
        }
        return;
    }
}