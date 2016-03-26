<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Cache;


use ACP3\Core\Filesystem;

/**
 * Class Purge
 * @package ACP3\Core\Cache
 */
class Purge
{
    /**
     * @param string|array $directory
     * @param string       $cacheId
     *
     * @return bool
     */
    public static function purge($directory, $cacheId = '')
    {
        if (is_array($directory)) {
            return self::handleMultipleDirectories($directory, $cacheId);
        }

        if (!is_file($directory) && !is_dir($directory)) {
            return true;
        }

        if (is_file($directory) === true) {
            return @unlink($directory);
        }

        self::purgeCurrentDirectory($directory, $cacheId);

        return true;
    }

    /**
     * @param array  $directory
     * @param string $cacheId
     *
     * @return bool
     */
    protected static function handleMultipleDirectories(array $directory, $cacheId)
    {
        foreach ($directory as $item) {
            static::purge($item, $cacheId);
        }

        return true;
    }

    /**
     * @param string $directory
     * @param string $cacheId
     */
    protected static function purgeCurrentDirectory($directory, $cacheId)
    {
        foreach (Filesystem::scandir($directory) as $file) {
            $path = "$directory/$file";

            if (is_dir($path)) {
                static::purge($path, $cacheId);
                if (empty($cacheId)) {
                    rmdir($path);
                }
            } elseif (empty($cacheId) || strpos($file, $cacheId) !== false) {
                unlink($path);
            }
        }
    }
}
